<?php

namespace App\Domains\Authentication\Services;

use App\Core\Enums\RoleType;
use App\Core\Enums\UserType;
use App\Core\Services\BaseService;
use App\Domains\Authentication\Data\RegisterUserData;
use App\Domains\Authentication\Events\UserRegistered;
use App\Models\User;

final class RegistrationService extends BaseService
{
    public function register(RegisterUserData $data): User
    {
        return $this->transaction(function () use ($data): User {
            $user = User::query()->create([
                'name' => $data->name,
                'email' => (string) $data->email,
                'password' => $data->password,
                'type' => $data->type,
                'phone' => $data->phone,
            ]);

            $role = match ($data->type) {
                UserType::Administrator => RoleType::Administrator->value,
                UserType::Staff => RoleType::Staff->value,
                UserType::Client => RoleType::Client->value,
            };

            $user->assignRole($role);

            event(new UserRegistered($user));

            return $user;
        });
    }
}
