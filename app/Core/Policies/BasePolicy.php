<?php

namespace App\Core\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;

abstract class BasePolicy
{
    use HandlesAuthorization;

    /**
     * Super-admins may bypass domain policies when a dedicated role exists later.
     */
    public function before(User $user, string $ability): ?bool
    {
        if (method_exists($user, 'hasRole') && $user->hasRole('super-admin')) {
            return true;
        }

        return null;
    }

    protected function owns(User $user, Model $model, string $attribute = 'user_id'): bool
    {
        return (string) $model->getAttribute($attribute) === (string) $user->getKey();
    }
}
