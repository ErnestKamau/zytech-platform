<?php

namespace App\Domains\Authentication\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Authentication\Services\SessionService;
use App\Models\Session;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.portal')]
#[Title('Sessions')]
final class Sessions extends BaseComponent
{
    public function revoke(SessionService $sessions, string $sessionId): void
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        if ($sessionId === session()->getId()) {
            $this->addError('sessions', 'You cannot revoke the current session from here. Sign out instead.');

            return;
        }

        $sessions->revoke($user, $sessionId);
        session()->flash('status', 'Session revoked.');
    }

    public function revokeOthers(SessionService $sessions): void
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        $sessions->revokeOthers($user, session()->getId());
        session()->flash('status', 'Other sessions revoked.');
    }

    public function render(SessionService $sessions): View
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        /** @var Collection<int, Session> $items */
        $items = $sessions->forUser($user);

        return view('livewire.authentication.sessions', [
            'sessions' => $items,
            'currentSessionId' => session()->getId(),
        ]);
    }
}
