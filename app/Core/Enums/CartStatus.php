<?php

namespace App\Core\Enums;

enum CartStatus: string
{
    case Active = 'active';
    case Converted = 'converted';
    case Abandoned = 'abandoned';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Converted => 'Converted',
            self::Abandoned => 'Abandoned',
        };
    }

    public function isOpen(): bool
    {
        return $this === self::Active;
    }
}
