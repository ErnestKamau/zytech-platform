<?php

namespace App\Core\Enums;

enum RoleType: string
{
    case SuperAdmin = 'super-admin';
    case Administrator = 'administrator';
    case Staff = 'staff';
    case Client = 'client';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Administrator',
            self::Administrator => 'Administrator',
            self::Staff => 'Staff',
            self::Client => 'Client',
        };
    }

    public function canAccessAdminPanel(): bool
    {
        return match ($this) {
            self::SuperAdmin, self::Administrator, self::Staff => true,
            self::Client => false,
        };
    }
}
