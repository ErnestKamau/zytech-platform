<?php

namespace App\Core\Enums;

enum ArticleType: string
{
    case Guide = 'guide';
    case CaseStudy = 'case-study';
    case Regulation = 'regulation';
    case CostGuide = 'cost-guide';
    case Material = 'material';
    case Planning = 'planning';
    case Maintenance = 'maintenance';
    case News = 'news';
    case Faq = 'faq';

    public function label(): string
    {
        return match ($this) {
            self::Guide => 'Construction guide',
            self::CaseStudy => 'Case study',
            self::Regulation => 'Building regulations',
            self::CostGuide => 'Cost guide',
            self::Material => 'Materials',
            self::Planning => 'Planning & approvals',
            self::Maintenance => 'Maintenance',
            self::News => 'Industry news',
            self::Faq => 'FAQ article',
        };
    }
}
