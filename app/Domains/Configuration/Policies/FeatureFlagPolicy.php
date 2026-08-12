<?php

namespace App\Domains\Configuration\Policies;

use App\Core\Policies\BasePolicy;
use App\Models\FeatureFlag;
use App\Models\User;

final class FeatureFlagPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('feature-flags.view') || $user->can('feature-flags.manage');
    }

    public function view(User $user, FeatureFlag $flag): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->can('feature-flags.manage');
    }

    public function update(User $user, FeatureFlag $flag): bool
    {
        return $user->can('feature-flags.manage');
    }

    public function delete(User $user, FeatureFlag $flag): bool
    {
        return $user->can('feature-flags.manage');
    }
}
