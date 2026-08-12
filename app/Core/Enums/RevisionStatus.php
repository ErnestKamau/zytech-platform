<?php

namespace App\Core\Enums;

enum RevisionStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Superseded = 'superseded';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Published => 'Published',
            self::Superseded => 'Superseded',
        };
    }
}
