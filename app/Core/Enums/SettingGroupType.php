<?php

namespace App\Core\Enums;

enum SettingGroupType: string
{
    case General = 'general';
    case Branding = 'branding';
    case Seo = 'seo';
    case Contact = 'contact';
    case Social = 'social';
    case Email = 'email';
    case Analytics = 'analytics';
    case Storage = 'storage';
    case Homepage = 'homepage';
    case Footer = 'footer';

    public function label(): string
    {
        return match ($this) {
            self::General => 'General',
            self::Branding => 'Branding',
            self::Seo => 'SEO',
            self::Contact => 'Contact',
            self::Social => 'Social',
            self::Email => 'Email',
            self::Analytics => 'Analytics',
            self::Storage => 'Storage',
            self::Homepage => 'Homepage',
            self::Footer => 'Footer',
        };
    }
}
