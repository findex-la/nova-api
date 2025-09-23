<?php

namespace Opscale\NovaAPI\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;

abstract class APIPolicy
{
    use HandlesAuthorization;

    abstract public function getResource(): string;

    final public function create(
        Model $model): bool
    {
        return $this->checkAbility($model, null, 'create');
    }

    final public function viewAny(
        Model $model): bool
    {
        return $this->checkAbility($model, null, 'read');
    }

    final public function view(
        Model $consumer,
        Model $model): bool
    {
        return $this->checkAbility($consumer, $model, 'read');
    }

    final public function update(
        Model $consumer,
        Model $model): bool
    {
        return $this->checkAbility($consumer, $model, 'update');
    }

    final public function delete(
        Model $consumer,
        Model $model): bool
    {
        return $this->checkAbility($consumer, $model, 'delete');
    }

    final protected function checkAbility(
        Model $consumer,
        ?Model $model,
        string $action): bool
    {
        $resource = $this->getResource();
        $ability = sprintf('%s:%s', $resource, $action);

        if (method_exists($consumer, 'tokenCan') === false) {
            return false;
        }

        return $consumer->tokenCan($ability);
    }
}
