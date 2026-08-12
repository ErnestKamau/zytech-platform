<?php

namespace App\Core\Enums;

enum ServiceType: string
{
    case Design = 'design';
    case Planning = 'planning';
    case Construction = 'construction';
    case SiteWorks = 'site-works';
    case Consulting = 'consulting';

    public function label(): string
    {
        return match ($this) {
            self::Design => 'Design',
            self::Planning => 'Planning',
            self::Construction => 'Construction',
            self::SiteWorks => 'Site works',
            self::Consulting => 'Consulting',
        };
    }
}
