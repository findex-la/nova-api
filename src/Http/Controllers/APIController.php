<?php

namespace Opscale\NovaAPI\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Opscale\NovaAPI\Http\Requests\APIRequest;
use Opscale\NovaAPI\Policies\APIPolicy;
use Orion\Http\Controllers\Controller;

class APIController extends Controller
{
    protected $resourceClass = null;

    public function __construct(Request $request)
    {
        $this->model = $this->resolveModel($request);
        $this->request = $this->resolveRequest($request);
        $this->policy = $this->resolvePolicy($request);
        parent::__construct($request);
    }

    public function resolveUser()
    {
        return Auth::guard('sanctum')->user();
    }

    public function resolveResource(Request $request)
    {
        if ($this->resourceClass != null) {
            return $this->resourceClass;
        } elseif (count($request->segments()) > 0) {
            $segments = $request->segments();
            $uriKey = end($segments);
            $this->resourceClass = collect(appResources())
                ->first(function ($resource) use ($uriKey) {
                    return $resource::uriKey() === $uriKey;
                });

            return $this->resourceClass;
        } else {
            throw new Exception('Resource not found');
        }
    }

    public function resolveRequest(Request $request): string
    {
        $class = get_class(new class extends APIRequest
        {
            protected $resource = null;

            public function getResource()
            {
                return $this->resource;
            }

            public function setResource(string $resource)
            {
                $this->resource = $resource;
            }
        });

        $resource = $this->resolveResource($request);
        App::singleton($class, function ($app) use ($class, $resource) {
            $instance = new $class;
            $instance->setResource($resource::uriKey());

            return $instance;
        });

        return $class;
    }

    public function resolvePolicy(Request $request): string
    {
        $class = get_class(new class extends APIPolicy
        {
            protected $resource = null;

            public function getResource()
            {
                return $this->resource;
            }

            public function setResource(string $resource)
            {
                $this->resource = $resource;
            }
        });

        $resource = $this->resolveResource($request);
        App::singleton($class, function ($app) use ($class, $resource) {
            $instance = new $class;
            $instance->setResource($resource::uriKey());

            return $instance;
        });

        return $class;
    }

    protected function resolveModel(Request $request): string
    {
        $resourceClass = $this->resolveResource($request);

        return $resourceClass::$model;
    }
}
