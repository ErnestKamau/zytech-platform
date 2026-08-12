<?php

namespace App\Domains\Portal\Policies;

use App\Core\Policies\BasePolicy;
use App\Models\PortalConversation;
use App\Models\User;

final class MessagePolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->clientProfile?->portal_access_granted_at !== null
            || $user->can('clients.manage');
    }

    public function view(User $user, PortalConversation $conversation): bool
    {
        return $conversation->client?->user_id === $user->id
            || $user->can('clients.manage');
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }
}
