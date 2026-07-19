<?php

namespace App\Core\Enums;

enum VisibilityStatus: string
{
    case Public = 'public';
    case Private = 'private';
    case Unlisted = 'unlisted';

    public function label(): string
    {
        return match ($this) {
            self::Public => 'Public',
            self::Private => 'Private',
            self::Unlisted => 'Unlisted',
        };
    }
}
