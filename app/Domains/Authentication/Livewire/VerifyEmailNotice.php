<?php

namespace App\Domains\Authentication\Livewire;

use App\Core\Livewire\BaseComponent;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.auth')]
#[Title('Verify email')]
final class VerifyEmailNotice extends BaseComponent
{
    public function mount(): void
    {
        $user = Auth::user();

        if ($user === null) {
            $this->redirect(route('login'), navigate: true);

            return;
        }

        if ($user->hasVerifiedEmail()) {
            $this->redirect(route('account.profile'), navigate: true);
        }
    }

    public function resend(): void
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        $user->sendEmailVerificationNotification();
        session()->flash('status', 'Verification link sent.');
    }

    /**
     * Dev/helper path: mark verified when signed URL hits VerifyEmailController —
     * this component only shows the notice screen.
     */
    public function render(): View
    {
        return view('livewire.authentication.verify-email');
    }
}
