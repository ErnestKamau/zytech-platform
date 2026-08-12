<?php

namespace App\Domains\Portal\Policies;

use App\Core\Policies\BasePolicy;
use App\Models\MeetingRequest;
use App\Models\User;

final class MeetingPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->clientProfile?->portal_access_granted_at !== null
            || $user->can('clients.manage');
    }

    public function view(User $user, MeetingRequest $meeting): bool
    {
        return $meeting->client?->user_id === $user->id
            || $user->can('clients.manage');
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, MeetingRequest $meeting): bool
    {
        return $this->view($user, $meeting);
    }
}
