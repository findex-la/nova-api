<?php

namespace Opscale\NovaAPI;

use Illuminate\Http\Request;
use Laravel\Nova\Menu\MenuItem;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool as NovaTool;
use Opscale\NovaAPI\Nova\AccessToken;

class Tool extends NovaTool
{
    public function boot()
    {
        Nova::script('nova-api', __DIR__ . '/../dist/js/tool.js');
        Nova::style('nova-api', __DIR__ . '/../dist/css/tool.css');
    }

    public function menu(Request $request)
    {
        return MenuItem::resource(AccessToken::class);
    }
}
