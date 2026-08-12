<?php

namespace App\Core\Enums;

enum ContactType: string
{
    case Email = 'email';
    case Phone = 'phone';
    case WhatsApp = 'whatsapp';
    case Address = 'address';

    public function label(): string
    {
        return match ($this) {
            self::Email => 'Email',
            self::Phone => 'Phone',
            self::WhatsApp => 'WhatsApp',
            self::Address => 'Address',
        };
    }
}
