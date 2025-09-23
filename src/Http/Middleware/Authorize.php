<?php

namespace Opscale\NovaAPI\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool as NovaTool;
use Opscale\NovaAPI\Tool;

class Authorize
{
    /**
     * @param  Closure(Request): Response  $next
     */
    final public function handle(Request $request, Closure $next): Response
    {
        /** @var NovaTool|null $tool */
        $tool = (new Collection(Nova::registeredTools()))->first([$this, 'matchesTool']);

        if ($tool !== null && $tool->authorize($request)) {
            return $next($request);
        }

        abort(403);
    }

    final public function matchesTool(mixed $tool): bool
    {
        return $tool instanceof Tool;
    }
}
