<?php

namespace App\Core\Enums;

enum AnnouncementType: string
{
    case General = 'general';
    case Maintenance = 'maintenance';
    case Marketing = 'marketing';
    case System = 'system';

    public function label(): string
    {
        return match ($this) {
            self::General => 'General',
            self::Maintenance => 'Maintenance',
            self::Marketing => 'Marketing',
            self::System => 'System',
        };
    }
}
