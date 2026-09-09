<?php

namespace App\Filament\Pages;

use App\Filament\Resources\FeatureFlags\FeatureFlagResource;
use App\Filament\Resources\NavigationMenus\NavigationMenuResource;
use App\Filament\Resources\SeoRedirects\SeoRedirectResource;
use App\Filament\Resources\Settings\SettingResource;
use Filament\Support\Icons\Heroicon;

class SettingsHub extends AdminHubPage
{
    protected static ?string $navigationLabel = 'Settings';

    protected static ?string $title = 'Settings';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?int $navigationSort = 13;

    protected function getHubSubheading(): ?string
    {
        return 'Configuration, flags, navigation, and SEO redirects.';
    }

    public function getHubSections(): array
    {
        return [
            [
                'title' => 'Configuration',
                'cards' => [
                    $this->hubCard('Settings', SettingResource::class, Heroicon::OutlinedCog6Tooth),
                    $this->hubCard('Feature flags', FeatureFlagResource::class, Heroicon::OutlinedFlag),
                    $this->hubCard('Navigation', NavigationMenuResource::class, Heroicon::OutlinedBars3),
                    $this->hubCard('SEO redirects', SeoRedirectResource::class, Heroicon::OutlinedArrowPath),
                ],
            ],
        ];
    }
}
