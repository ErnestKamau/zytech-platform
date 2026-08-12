<?php

namespace App\Domains\Portal\Repositories;

use App\Models\Client;
use App\Models\User;

final class PortalRepository
{
    public function clientForUser(User $user): ?Client
    {
        return Client::query()
            ->with(['assignedSales', 'preferences'])
            ->where('user_id', $user->id)
            ->whereNotNull('portal_access_granted_at')
            ->first();
    }
}
