<?php

namespace App\Domains\Communication\Policies;

use App\Core\Policies\BasePolicy;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

abstract class CommunicationManagedPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('clients.view') || $user->can('clients.manage') || $user->can('settings.manage');
    }

    public function view(User $user, Model $model): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->can('clients.manage') || $user->can('settings.manage');
    }

    public function update(User $user, Model $model): bool
    {
        return $this->create($user);
    }

    public function delete(User $user, Model $model): bool
    {
        return $this->create($user);
    }
}
