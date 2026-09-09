<?php

namespace App\Domains\Authentication\Support;

use App\Domains\Portal\Repositories\PortalRepository;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

final class AuthenticatedHome
{
    public static function url(?User $user = null): string
    {
        $user ??= Auth::user();

        if ($user !== null && app(PortalRepository::class)->clientForUser($user) !== null) {
            return route('portal.dashboard');
        }

        return route('account.profile');
    }
}
