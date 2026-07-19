<?php

namespace App\Core\Enums;

enum ProjectStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Featured = 'featured';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Published => 'Published',
            self::Featured => 'Featured',
            self::Archived => 'Archived',
        };
    }
}
