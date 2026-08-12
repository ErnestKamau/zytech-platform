<?php

namespace App\Core\Enums;

enum MediaCollection: string
{
    case Hero = 'hero';
    case Gallery = 'gallery';
    case Documents = 'documents';
    case Videos = 'videos';
    case Logos = 'logos';
    case Team = 'team';
    case Knowledge = 'knowledge';
    case Testimonials = 'testimonials';
    case Homepage = 'homepage';
    case Projects = 'projects';
    case Services = 'services';
    case Certificates = 'certificates';
    case Downloads = 'downloads';
    case Company = 'company';
    case Seo = 'seo';

    public function label(): string
    {
        return match ($this) {
            self::Hero => 'Hero',
            self::Gallery => 'Gallery',
            self::Documents => 'Documents',
            self::Videos => 'Videos',
            self::Logos => 'Logos',
            self::Team => 'Team',
            self::Knowledge => 'Knowledge',
            self::Testimonials => 'Testimonials',
            self::Homepage => 'Homepage',
            self::Projects => 'Projects',
            self::Services => 'Services',
            self::Certificates => 'Certificates',
            self::Downloads => 'Downloads',
            self::Company => 'Company',
            self::Seo => 'SEO',
        };
    }

    public function isPrivate(): bool
    {
        return match ($this) {
            self::Documents, self::Certificates, self::Downloads => true,
            default => false,
        };
    }

    public function acceptsConversions(): bool
    {
        return match ($this) {
            self::Videos, self::Documents, self::Certificates, self::Downloads => false,
            default => true,
        };
    }
}
