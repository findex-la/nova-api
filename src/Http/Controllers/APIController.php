<?php

namespace Opscale\NovaAPI\Http\Controllers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Orion\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class APIController extends Controller
{
    /**
     * @var class-string<\Laravel\Nova\Resource<\Illuminate\Database\Eloquent\Model>>|null
     */
    protected ?string $resourceClass = null;

    final public function __construct(Request $request)
    {
        $this->model = $this->resolveModel($request);
        $this->request = $this->resolveRequest($request);
        $this->policy = $this->resolvePolicy($request);

        // This constructror call must be at the end to ensure
        // properties are set before parent constructor logic
        parent::__construct();
    }

    /**
     * @phpstan-ignore solid.lsp.parentCall
     */
    final public function resolveUser(): ?Authenticatable
    {
        return Auth::guard('sanctum')->user();
    }

    /**
     * @return class-string<\Laravel\Nova\Resource<\Illuminate\Database\Eloquent\Model>>
     */
    final public function resolveResource(Request $request): string
    {
        if ($this->resourceClass !== null) {
            return $this->resourceClass;
        }

        $segments = $request->segments();
        if (count($segments) < 2) {
            (new JsonResponse(
                ['error' => 'Resource not found'],
                Response::HTTP_NOT_FOUND))->send();
            exit;
        }

        // For routes like /api/users or /api/users/123, we want the second segment (users)
        $uriKey = $segments[1];
        /** @var array<int, class-string<\Laravel\Nova\Resource<\Illuminate\Database\Eloquent\Model>>> $configuredResources */
        $configuredResources = Config::get('nova-api.resources', []);
        $resources = new Collection($configuredResources);

        /** @var class-string<\Laravel\Nova\Resource<\Illuminate\Database\Eloquent\Model>>|null $resourceClass */
        $resourceClass = $resources
            ->first(function (mixed $resource) use ($uriKey): bool {
                /** @var class-string<\Laravel\Nova\Resource<\Illuminate\Database\Eloquent\Model>> $resource */
                return $resource::uriKey() === $uriKey;
            });

        if ($resourceClass === null) {
            (new JsonResponse(
                ['error' => 'Resource not found'],
                Response::HTTP_NOT_FOUND))->send();
            exit;
        }

        $this->resourceClass = $resourceClass;

        return $this->resourceClass;
    }

    final public function resolveRequest(Request $request): string
    {
        $resource = $this->resolveResource($request);
        $binding = $resource::uriKey() . '-request';

        return App::getAlias($binding);
    }

    /**
     * @phpstan-ignore solid.ocp.conditionalOverride
     */
    public function resolvePolicy(Request $request): string
    {
        $resource = $this->resolveResource($request);
        $binding = $resource::uriKey() . '-policy';

        return App::getAlias($binding);
    }

    /**
     * @phpstan-ignore solid.ocp.conditionalOverride
     */
    protected function resolveModel(Request $request): string
    {
        $resourceClass = $this->resolveResource($request);

        return $resourceClass::$model;
    }
}
