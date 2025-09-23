<?php

namespace Opscale\NovaAPI\Http\Requests;

use Orion\Http\Requests\Request;

abstract class APIRequest extends Request
{
    abstract public function getResource(): string;

    /**
     * @return array<string, mixed>
     */
    final public function commonRules(): array
    {
        $rules = parent::commonRules();
        $resource = $this->getResource();

        if (! class_exists($resource) || ! property_exists($resource, 'model')) {
            return $rules;
        }

        /** @var class-string<\Illuminate\Database\Eloquent\Model> $model */
        $model = $resource::$model;

        if (! class_exists($model)) {
            return $rules;
        }

        $modelInstance = new $model;
        if (! property_exists($modelInstance, 'validationRules')) {
            return $rules;
        }

        return array_merge($rules, $modelInstance->validationRules);
    }
}
