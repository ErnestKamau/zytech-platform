<?php

namespace App\Core\Enums;

enum AwardCategory: string
{
    case Industry = 'industry';
    case Safety = 'safety';
    case Design = 'design';
    case Community = 'community';

    public function label(): string
    {
        return match ($this) {
            self::Industry => 'Industry',
            self::Safety => 'Safety',
            self::Design => 'Design',
            self::Community => 'Community',
        };
    }
}
