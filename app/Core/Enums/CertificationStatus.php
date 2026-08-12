<?php

namespace App\Core\Enums;

enum CertificationStatus: string
{
    case Active = 'active';
    case Pending = 'pending';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Pending => 'Pending',
            self::Expired => 'Expired',
        };
    }
}
