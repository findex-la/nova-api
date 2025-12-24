<?php

namespace Opscale\NovaAPI\Services\Actions;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Opscale\Actions\Action;
use Opscale\NovaAPI\Events\AccessTokenGenerated;

class CacheToken extends Action
{
    final public function identifier(): string
    {
        return 'cache-token';
    }

    final public function name(): string
    {
        return 'Cache Token';
    }

    final public function description(): string
    {
        return 'Caches an access token for temporary retrieval';
    }

    /**
     * @return array<int, array{name: string, description: string, type: string, rules: array<int, string>}>
     */
    final public function parameters(): array
    {
        return [
            [
                'name' => 'tokenId',
                'description' => 'The unique identifier of the token',
                'type' => 'string',
                'rules' => ['required', 'string'],
            ],
            [
                'name' => 'token',
                'description' => 'The plain text token to cache',
                'type' => 'string',
                'rules' => ['required', 'string'],
            ],
        ];
    }

    /**
     * @param  array{tokenId?: string, token?: string}  $attributes
     * @return array<string, bool>
     */
    final public function handle(array $attributes = []): array
    {
        $this->fill($attributes);

        /** @var array{tokenId: string, token: string} $validated */
        $validated = $this->validateAttributes();

        $cacheKey = 'opscale.api.token.' . $validated['tokenId'];

        Cache::put(
            $cacheKey,
            $validated['token'],
            Carbon::now()->addMinutes(5)
        );

        return ['success' => true];
    }

    final public function asListener(AccessTokenGenerated $accessTokenGenerated): void
    {
        /** @var int|string $id */
        $id = $accessTokenGenerated->newAccessToken->accessToken->getKey();
        /** @var string $token */
        $token = $accessTokenGenerated->newAccessToken->plainTextToken;

        $this->handle(['tokenId' => (string) $id, 'token' => $token]);
    }
}
