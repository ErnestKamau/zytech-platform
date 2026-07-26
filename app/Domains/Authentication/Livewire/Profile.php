<?php

namespace App\Domains\Authentication\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\User\Actions\UpdateProfile;
use App\Domains\User\Data\UpdateProfileData;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.portal')]
#[Title('Profile')]
final class Profile extends BaseComponent
{
    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public function mount(): void
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = (string) ($user->phone ?? '');
    }

    public function save(UpdateProfile $action): void
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:40'],
        ]);

        $action->handle($user, UpdateProfileData::fromArray([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone !== '' ? $this->phone : null,
        ]));

        session()->flash('status', 'Profile updated.');
    }

    public function render(): View
    {
        return view('livewire.authentication.profile');
    }
}
