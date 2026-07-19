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
        };
    }
}
