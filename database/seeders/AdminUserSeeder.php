<?php

namespace Database\Seeders;

use App\Core\Enums\RoleType;
use App\Core\Enums\UserType;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@zytech.local'],
            [
                'name' => 'Zytech Admin',
                'password' => 'password',
                'type' => UserType::Administrator,
                'email_verified_at' => now(),
            ],
        );

        $admin->syncRoles([RoleType::SuperAdmin->value, RoleType::Administrator->value]);

        $staff = User::query()->updateOrCreate(
            ['email' => 'staff@zytech.local'],
            [
                'name' => 'Zytech Staff',
                'password' => 'password',
                'type' => UserType::Staff,
                'email_verified_at' => now(),
            ],
        );

        $staff->syncRoles([RoleType::Staff->value]);

        $client = User::query()->updateOrCreate(
            ['email' => 'client@zytech.local'],
            [
                'name' => 'Zytech Client',
                'password' => 'password',
                'type' => UserType::Client,
                'email_verified_at' => now(),
            ],
        );

        $client->syncRoles([RoleType::Client->value]);
    }
}
