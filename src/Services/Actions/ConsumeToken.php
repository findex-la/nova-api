<?php

namespace Opscale\NovaAPI\Services\Actions;

use Illuminate\Support\Facades\Cache;
use Lorisleiva\Actions\Action;

class ConsumeToken extends Action
{
    final public function handle(string $tokenId): string
    {
        $cacheKey = 'opscale.api.token.' . $tokenId;

        /** @var string $token */
        $token = Cache::get($cacheKey, '');

        return $token;
    }
}
