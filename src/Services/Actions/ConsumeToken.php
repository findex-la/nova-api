<?php

namespace Opscale\NovaAPI\Services\Actions;

use Illuminate\Support\Facades\Cache;
use Opscale\Actions\Action;

class ConsumeToken extends Action
{
    final public function identifier(): string
    {
        return 'consume-token';
    }

    final public function name(): string
    {
        return 'Consume Token';
    }

    final public function description(): string
    {
        return 'Retrieves a cached token by its ID';
    }

    /**
     * @return array<int, array{name: string, description: string, type: string, rules: array<int, string>}>
     */
    final public function parameters(): array
    {
        return [
            [
                'name' => 'tokenId',
                'description' => 'The unique identifier of the token to retrieve',
                'type' => 'string',
                'rules' => ['required', 'string'],
            ],
        ];
    }

    /**
     * @param  array{tokenId?: string}  $attributes
     * @return array{token: string}
     */
    final public function handle(array $attributes = []): array
    {
        $this->fill($attributes);

        /** @var array{tokenId: string} $validated */
        $validated = $this->validateAttributes();

        $cacheKey = 'opscale.api.token.' . $validated['tokenId'];

        /** @var string $token */
        $token = Cache::get($cacheKey, '');

        return ['token' => $token];
    }
}
