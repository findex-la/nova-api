<?php

namespace Opscale\NovaAPI\Policies;

use Exception;
use Illuminate\Auth\Access\HandlesAuthorization;

abstract class APIPolicy
{
    use HandlesAuthorization;

    abstract public function getResource();

    public function create($user)
    {
        return $this->checkAbility($user, null, 'create');
    }

    public function viewAny($user)
    {
        return $this->checkAbility($user, null, 'read');
    }

    public function view($user, $model)
    {
        return $this->checkAbility($user, $model, 'read');
    }

    public function update($user, $model)
    {
        return $this->checkAbility($user, $model, 'update');
    }

    public function delete($user, $model)
    {
        return $this->checkAbility($user, $model, 'delete');
    }

    protected function checkAbility($user, $model, $action)
    {
        try {
            $resource = $this->getResource();
            $ability = "{$resource}:{$action}";

            return $user->tokenCan($ability);
        } catch (Exception $e) {
            return false;
        }
    }
}
