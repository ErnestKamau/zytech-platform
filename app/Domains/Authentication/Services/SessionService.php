<?php

namespace App\Domains\Authentication\Services;

use App\Core\Services\BaseService;
use App\Models\Session;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class SessionService extends BaseService
{
    /**
     * @return Collection<int, Session>
     */
    public function forUser(User $user): Collection
    {
        return Session::query()
            ->where('user_id', $user->id)
            ->orderByDesc('last_activity')
            ->get();
    }

    public function revoke(User $user, string $sessionId): void
    {
        Session::query()
            ->where('user_id', $user->id)
            ->where('id', $sessionId)
            ->delete();
    }

    public function revokeOthers(User $user, string $currentSessionId): void
    {
        Session::query()
            ->where('user_id', $user->id)
            ->where('id', '!=', $currentSessionId)
            ->delete();
    }

    public function revokeAll(User $user): void
    {
        Session::query()
            ->where('user_id', $user->id)
            ->delete();
    }

    public function touchCurrent(User $user): void
    {
        DB::table('sessions')
            ->where('id', session()->getId())
            ->update([
                'user_id' => $user->id,
                'last_activity' => now()->getTimestamp(),
            ]);
    }
}
