<?php

namespace App\Domains\User\Policies;

use App\Core\Policies\BasePolicy;
use App\Models\User;

final class UserPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('users.view');
    }

    public function view(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->can('users.view');
    }

    public function create(User $user): bool
    {
        return $user->can('users.create');
    }

    public function update(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->can('users.update');
    }

    public function delete(User $user, User $model): bool
    {
        return $user->id !== $model->id && $user->can('users.delete');
    }

    public function lock(User $user, User $model): bool
    {
        return $user->id !== $model->id && $user->can('users.lock');
    }

    public function assignRole(User $user, User $model): bool
    {
        return $user->can('users.assign-role');
    }
}
