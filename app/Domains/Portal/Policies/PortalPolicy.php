<?php

namespace App\Domains\Portal\Policies;

use App\Core\Policies\BasePolicy;
use App\Models\Client;
use App\Models\User;

final class PortalPolicy extends BasePolicy
{
    public function access(User $user, ?Client $client = null): bool
    {
        if ($client === null) {
            return $user->clientProfile !== null
                && $user->clientProfile->portal_access_granted_at !== null;
        }

        return $client->user_id === $user->id
            && $client->portal_access_granted_at !== null;
    }
}
