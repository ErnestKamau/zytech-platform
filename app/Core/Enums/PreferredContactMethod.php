<?php

namespace App\Core\Enums;

enum PreferredContactMethod: string
{
    case Email = 'email';
    case Phone = 'phone';
    case WhatsApp = 'whatsapp';

    public function label(): string
    {
        return match ($this) {
            self::Email => 'Email',
            self::Phone => 'Phone',
            self::WhatsApp => 'WhatsApp',
        };
    }
}
