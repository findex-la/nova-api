<?php

namespace Opscale\NovaAPI\Http\Requests;

use Orion\Http\Requests\Request;

abstract class APIRequest extends Request
{
    abstract public function getResource();

    public function commonRules(): array
    {
        $resource = $this->getResource();
        $model = $resource::$model;
        if (method_exists($model, 'rules')) {
            return $model::rules();
        } else {
            return [];
        }
    }

    public function storeRules(): array
    {
        return $this->commonRules();
    }
}
