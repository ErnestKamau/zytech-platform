<?php

namespace App\Core\Enums;

enum ConstructionStage: string
{
    case Planning = 'planning';
    case Approvals = 'approvals';
    case SitePreparation = 'site-preparation';
    case Foundation = 'foundation';
    case Structure = 'structure';
    case Roofing = 'roofing';
    case Finishes = 'finishes';
    case Inspection = 'inspection';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Planning => 'Planning',
            self::Approvals => 'Approvals',
            self::SitePreparation => 'Site preparation',
            self::Foundation => 'Foundation',
            self::Structure => 'Structure',
            self::Roofing => 'Roofing',
            self::Finishes => 'Finishes',
            self::Inspection => 'Inspection',
            self::Completed => 'Completed',
        };
    }
}
