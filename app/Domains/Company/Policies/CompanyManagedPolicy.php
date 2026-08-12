<?php

namespace App\Domains\Company\Policies;

use App\Core\Policies\BasePolicy;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

abstract class CompanyManagedPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('company.view') || $user->can('company.update');
    }

    public function view(User $user, Model $model): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->can('company.update');
    }

    public function update(User $user, Model $model): bool
    {
        return $user->can('company.update');
    }

    public function delete(User $user, Model $model): bool
    {
        return $user->can('company.update');
    }
}
