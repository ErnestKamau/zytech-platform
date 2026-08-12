<?php

namespace App\Core\Enums;

enum ConversionType: string
{
    case Thumb = 'thumb';
    case Small = 'small';
    case Medium = 'medium';
    case Large = 'large';
    case Hero = 'hero';
    case Webp = 'webp';

    public function label(): string
    {
        return match ($this) {
            self::Thumb => 'Thumbnail',
            self::Small => 'Small',
            self::Medium => 'Medium',
            self::Large => 'Large',
            self::Hero => 'Hero',
            self::Webp => 'WebP',
        };
    }

    public function width(): int
    {
        return match ($this) {
            self::Thumb => 320,
            self::Small => 640,
            self::Medium => 1280,
            self::Large => 1920,
            self::Hero => 2400,
            self::Webp => 1280,
        };
    }
}
