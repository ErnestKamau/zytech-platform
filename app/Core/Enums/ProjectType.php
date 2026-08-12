<?php

namespace App\Core\Enums;

enum ProjectType: string
{
    case Residential = 'residential';
    case Commercial = 'commercial';
    case Industrial = 'industrial';
    case Landscaping = 'landscaping';
    case Structural = 'structural';
    case MixedUse = 'mixed-use';

    public function label(): string
    {
        return match ($this) {
            self::Residential => 'Residential',
            self::Commercial => 'Commercial',
            self::Industrial => 'Industrial',
            self::Landscaping => 'Landscaping',
            self::Structural => 'Structural',
            self::MixedUse => 'Mixed use',
        };
    }
}
