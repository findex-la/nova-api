<?php

namespace Opscale\NovaAPI\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Laravel\Sanctum\NewAccessToken;

class AccessTokenGenerated
{
    use Dispatchable;

    /**
     * Create a new event instance.
     */
    final public function __construct(
        public readonly NewAccessToken $newAccessToken
    ) {}
}
