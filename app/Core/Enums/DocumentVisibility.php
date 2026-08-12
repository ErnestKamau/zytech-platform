<?php

namespace App\Core\Enums;

enum DocumentVisibility: string
{
    case Staff = 'staff';
    case Client = 'client';
    case Private = 'private';

    public function label(): string
    {
        return match ($this) {
            self::Staff => 'Staff only',
            self::Client => 'Visible to client',
            self::Private => 'Private',
        };
    }
}
