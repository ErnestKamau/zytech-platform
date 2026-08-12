<?php

namespace App\Core\Enums;

enum MeetingType: string
{
    case SiteVisit = 'site-visit';
    case Consultation = 'consultation';
    case Virtual = 'virtual';
    case DesignReview = 'design-review';
    case ProjectReview = 'project-review';
    case Inspection = 'inspection';

    public function label(): string
    {
        return match ($this) {
            self::SiteVisit => 'Site visit',
            self::Consultation => 'Consultation',
            self::Virtual => 'Virtual meeting',
            self::DesignReview => 'Design review',
            self::ProjectReview => 'Project review',
            self::Inspection => 'Completion inspection',
        };
    }
}
