<?php

namespace Opscale\NovaAPI\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Actions\Actionable;
use Laravel\Sanctum\PersonalAccessToken;

class AccessToken extends PersonalAccessToken
{
    use Actionable;

    public $table = 'personal_access_tokens';

    protected static function rules(string $property)
    {
        $rules = [
            'name' => ['required'],
            'abilities' => ['nullable', 'gt:0'],
        ];

        return isset($rules[$property]) ? $rules[$property] : null;
    }

    // Overriding insertAndSetId method to create a token via Sanctum API
    // to keep in sync the Laravel Nova model with the Sanctum model
    protected function insertAndSetId(Builder $query, $attributes)
    {
        $context = Auth::user();
        $token = null;

        if ($this->abilities) {
            $abilities = collect($this->abilities)->flatMap(function ($item) {
                return collect($item['fields']['actions'])
                    ->map(function ($action) use ($item) {
                        return strtolower($item['fields']['resource']) . ':' . $action;
                    });
            })->all();

            $token = $context->createToken(
                $this->name,
                $abilities,
                $this->expires_at);
        } else {
            $token = $context->createToken(
                $this->name,
                ['*'],
                $this->expires_at);
        }

        $id = $token->accessToken->id;
        $keyName = $this->getKeyName();
        $this->setAttribute($keyName, $id);
        cache()->put(
            'opscale.api.token.' . $id,
            $token->plainTextToken,
            now()->addMinutes(15));
    }
}
