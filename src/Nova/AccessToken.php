<?php

namespace Opscale\NovaAPI\Nova;

use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\MultiSelect;
use Laravel\Nova\Fields\Repeater;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Resource;
use Opscale\NovaAPI\Models\AccessToken as Model;
use Opscale\NovaAPI\Nova\Repeaters\Ability;

class AccessToken extends Resource
{
    public static $model = Model::class;

    public static $title = 'name';

    public static $search = [
        'name',
    ];

    public static function label()
    {
        return _('API Tokens');
    }

    public static function singularLabel()
    {
        return _('API Token');
    }

    public static function uriKey()
    {
        return _('api-tokens');
    }

    public function fields(NovaRequest $request)
    {
        return [
            Text::make(_('Name'), 'name')
                ->rules(Model::rules('name'))
                ->sortable(),

            Date::make(_('Expiration'), 'expires_at')
                ->nullable()
                ->sortable()
                ->filterable(),

            DateTime::make(_('Last used'), 'last_used_at')
                ->exceptOnForms()
                ->sortable()
                ->filterable(),

            DateTime::make(_('Created'), 'created_at')
                ->exceptOnForms()
                ->sortable()
                ->filterable(),

            Repeater::make(_('Abilities'), 'abilities')
                ->repeatables([
                    Ability::make(),
                ])
                ->asJson()
                ->onlyOnForms()
                ->hideWhenUpdating(),

            MultiSelect::make(_('Abilities'), 'abilities')
                ->options(fn () => collect($this->resource->abilities)
                    ->mapWithKeys(function ($ability) {
                        return [$ability => $ability];
                    }))
                ->displayUsingLabels()
                ->rules(Model::rules('abilities'))
                ->hideWhenCreating()
                ->hideFromIndex(),

            Text::make(_('Token'),
                fn () => cache()->get('opscale.api.token.' . $this->id))
                ->copyable()
                ->onlyOnDetail()
                ->canSee(fn ($r) => cache()->has('opscale.api.token.' . $this->id)),
        ];
    }
}
