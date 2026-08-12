<?php

namespace App\Domains\Portal\Policies;

use App\Core\Policies\BasePolicy;
use App\Models\SupportTicket;
use App\Models\User;

final class SupportPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->clientProfile?->portal_access_granted_at !== null
            || $user->can('clients.manage');
    }

    public function view(User $user, SupportTicket $ticket): bool
    {
        return $ticket->client?->user_id === $user->id
            || $user->can('clients.manage');
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, SupportTicket $ticket): bool
    {
        return $this->view($user, $ticket);
    }
}
