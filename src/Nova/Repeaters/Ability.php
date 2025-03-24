<?php

namespace Opscale\NovaAPI\Nova\Repeaters;

use Laravel\Nova\Fields\MultiSelect;
use Laravel\Nova\Fields\Repeater\Repeatable;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;

class Ability extends Repeatable
{
    public function fields(NovaRequest $request): array
    {
        $resources = collect(appResources())->mapWithKeys(function ($resource) {
            return [$resource::uriKey() => $resource::singularLabel()];
        })->toArray();

        return [
            Select::make(_('Resource'), 'resource')
                ->options($resources)
                ->displayUsingLabels()
                ->rules('required'),

            MultiSelect::make(_('Actions'), 'actions')
                ->options([
                    'create' => _('Create'),
                    'read' => _('Read'),
                    'update' => _('Update'),
                    'delete' => _('Delete'),
                ])
                ->displayUsingLabels()
                ->rules('required'),
        ];
    }
}
