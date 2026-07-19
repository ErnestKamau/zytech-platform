<?php

namespace App\Core\Enums;

enum UserType: string
{
    case Administrator = 'administrator';
    case Staff = 'staff';
    case Client = 'client';

    public function label(): string
    {
        return match ($this) {
            self::Administrator => 'Administrator',
            self::Staff => 'Staff',
            self::Client => 'Client',
        };
    }
}
