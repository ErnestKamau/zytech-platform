<?php

namespace App\Domains\Product\Policies;

use App\Core\Policies\BasePolicy;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

abstract class ProductManagedPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('products.view') || $user->can('products.manage');
    }

    public function view(User $user, Model $model): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->can('products.manage');
    }

    public function update(User $user, Model $model): bool
    {
        return $user->can('products.manage');
    }

    public function delete(User $user, Model $model): bool
    {
        return $user->can('products.manage');
    }
}
