<?php

namespace Opscale\NovaAPI;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;
use Opscale\NovaAPI\Http\Controllers\APIController;
use Opscale\NovaAPI\Http\Middleware\Authorize;
use Orion\Facades\Orion;

class ToolServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutes();

        Nova::serving(function (ServingNova $event) {
            $this->loadResources();
        });
    }

    public function register()
    {
        //
    }

    protected function loadResources()
    {
        Nova::resources([
            \Opscale\NovaAPI\Nova\AccessToken::class,
        ]);
    }

    protected function loadRoutes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware(['nova', Authorize::class])
            ->prefix('nova-vendor/opscale-co/nova-api')
            ->group(__DIR__ . '/../routes/api.php');

        $resources = appResources();
        Route::prefix('api')->group(function () use ($resources) {
            foreach ($resources as $resource) {
                Orion::resource($resource::uriKey(), APIController::class);
            }
        });
    }
}
