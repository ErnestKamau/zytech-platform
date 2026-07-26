<?php

namespace App\Domains\User\Policies;

use App\Core\Policies\BasePolicy;
use App\Models\Role;
use App\Models\User;

final class RolePolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('roles.view');
    }

    public function view(User $user, Role $role): bool
    {
        return $user->can('roles.view');
    }

    public function create(User $user): bool
    {
        return $user->can('roles.create');
    }

    public function update(User $user, Role $role): bool
    {
        return $user->can('roles.update');
    }

    public function delete(User $user, Role $role): bool
    {
        return $role->name !== 'super-admin' && $user->can('roles.delete');
    }
}
