<?php

namespace App\Domains\User\Services;

use App\Core\Services\BaseService;
use App\Domains\User\Data\UpdateProfileData;
use App\Models\User;

final class ProfileService extends BaseService
{
    public function update(User $user, UpdateProfileData $data): User
    {
        $emailChanged = strtolower($user->email) !== strtolower((string) $data->email);

        $user->fill([
            'name' => $data->name,
            'email' => (string) $data->email,
            'phone' => $data->phone,
            'preferences' => $data->preferences ?? $user->preferences,
        ]);

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        return $user->refresh();
    }
}
