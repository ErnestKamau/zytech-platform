<?php

namespace App\Domains\Authentication\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\User\Actions\UpdateProfile;
use App\Domains\User\Data\UpdateProfileData;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;

#[Layout('layouts.portal')]
#[Title('Profile')]
final class Profile extends BaseComponent
{
    use WithFileUploads;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public $avatar = null;

    public function mount(): void
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        $this->hydrateFromUser();
    }

    public function cancel(): void
    {
        $this->reset('avatar');
        $this->resetErrorBag();
        $this->hydrateFromUser();
    }

    public function updatedAvatar(): void
    {
        $this->validate([
            'avatar' => ['nullable', 'image', 'max:5120'],
        ]);
    }

    public function removeAvatar(): void
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        if (filled($user->avatar_path)) {
            Storage::disk('public')->delete($user->avatar_path);
            $user->forceFill(['avatar_path' => null])->save();
        }

        $this->reset('avatar');
        session()->flash('status', 'Profile photo removed.');
    }

    public function save(UpdateProfile $action): void
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:40'],
            'avatar' => ['nullable', 'image', 'max:5120'],
        ]);

        $action->handle($user, UpdateProfileData::fromArray([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone !== '' ? $this->phone : null,
        ]));

        if ($this->avatar !== null) {
            if (filled($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }

            $path = $this->avatar->store('avatars/'.$user->id, 'public');
            $user->forceFill(['avatar_path' => $path])->save();
            $this->reset('avatar');
        }

        $this->hydrateFromUser();
        session()->flash('status', 'Profile updated.');
    }

    public function render(): View
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        $initials = collect(explode(' ', (string) $user->name))
            ->filter()
            ->take(2)
            ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');

        return view('livewire.authentication.profile', [
            'avatarUrl' => $user->fresh()?->avatarUrl(),
            'initials' => $initials,
        ]);
    }

    private function hydrateFromUser(): void
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = (string) ($user->phone ?? '');
    }
}
