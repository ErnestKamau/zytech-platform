<?php

namespace App\Domains\User\Policies;

use App\Core\Policies\BasePolicy;
use App\Models\Permission;
use App\Models\User;

final class PermissionPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('permissions.view');
    }

    public function view(User $user, Permission $permission): bool
    {
        return $user->can('permissions.view');
    }

    public function create(User $user): bool
    {
        return $user->can('permissions.create');
    }

    public function update(User $user, Permission $permission): bool
    {
        return $user->can('permissions.update');
    }

    public function delete(User $user, Permission $permission): bool
    {
        return $user->can('permissions.delete');
    }
}
