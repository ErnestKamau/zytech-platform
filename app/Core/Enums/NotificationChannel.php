<?php

namespace App\Core\Enums;

enum NotificationChannel: string
{
    case Mail = 'mail';
    case Database = 'database';
    case Broadcast = 'broadcast';
    case Portal = 'portal';
    case Sms = 'sms';

    public function label(): string
    {
        return match ($this) {
            self::Mail => 'Email (Resend)',
            self::Database => 'In-app',
            self::Broadcast => 'Realtime',
            self::Portal => 'Client portal',
            self::Sms => 'SMS (Twilio)',
        };
    }
}
