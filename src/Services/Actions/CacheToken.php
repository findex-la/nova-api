<?php

namespace Opscale\NovaAPI\Services\Actions;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Action;
use Opscale\NovaAPI\Events\AccessTokenGenerated;

class CacheToken extends Action
{
    final public function handle(string $tokenId, string $token): void
    {
        $cacheKey = 'opscale.api.token.' . $tokenId;

        Cache::put(
            $cacheKey,
            $token,
            Carbon::now()->addMinutes(5)
        );
    }

    final public function asListener(AccessTokenGenerated $accessTokenGenerated): void
    {
        /** @var int|string $id */
        $id = $accessTokenGenerated->newAccessToken->accessToken->getKey();
        /** @var string $token */
        $token = $accessTokenGenerated->newAccessToken->plainTextToken;

        $this->handle((string) $id, $token);
    }
}
