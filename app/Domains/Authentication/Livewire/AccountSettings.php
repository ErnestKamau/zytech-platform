<?php

namespace App\Domains\Authentication\Livewire;

use App\Core\Livewire\BaseComponent;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.portal')]
#[Title('Account')]
final class AccountSettings extends BaseComponent
{
    public function render(): View
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        return view('livewire.authentication.account-settings', [
            'user' => $user,
        ]);
    }
}
