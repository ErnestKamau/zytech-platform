<?php

namespace App\Core\Enums;

enum NavigationLocation: string
{
    case Header = 'header';
    case Footer = 'footer';
    case Mobile = 'mobile';

    public function label(): string
    {
        return match ($this) {
            self::Header => 'Header',
            self::Footer => 'Footer',
            self::Mobile => 'Mobile',
        };
    }
}
