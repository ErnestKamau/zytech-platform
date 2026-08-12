<?php

namespace App\Domains\Configuration\Support;

final class ConfigurationCache
{
    public const SETTINGS_ALL = 'settings.all';

    public const BRANDING = 'settings.branding';

    public const SEO = 'settings.seo';

    public const CONTACT = 'settings.contact';

    public const SOCIAL = 'settings.social';

    public const FEATURE_FLAGS = 'feature-flags.all';

    public static function navigation(string $location): string
    {
        return "navigation.{$location}";
    }

    /**
     * @return list<string>
     */
    public static function settingsKeys(): array
    {
        return [
            self::SETTINGS_ALL,
            self::BRANDING,
            self::SEO,
            self::CONTACT,
            self::SOCIAL,
        ];
    }
}
