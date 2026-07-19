<?php

namespace App\Core\Enums;

enum NotificationChannel: string
{
    case Mail = 'mail';
    case Database = 'database';
    case Broadcast = 'broadcast';

    public function label(): string
    {
        return match ($this) {
            self::Mail => 'Email',
            self::Database => 'Database',
            self::Broadcast => 'Broadcast',
        };
    }
}
