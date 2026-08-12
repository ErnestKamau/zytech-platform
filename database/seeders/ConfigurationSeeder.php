<?php

namespace Database\Seeders;

use App\Core\Enums\FeatureStatus;
use App\Core\Enums\NavigationLocation;
use App\Core\Enums\SettingGroupType;
use App\Core\Enums\SettingType;
use App\Models\FeatureFlag;
use App\Models\NavigationItem;
use App\Models\NavigationMenu;
use App\Models\Setting;
use App\Models\SettingGroup;
use Illuminate\Database\Seeder;

class ConfigurationSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedGroupsAndSettings();
        $this->seedFeatureFlags();
        $this->seedNavigation();
    }

    private function seedGroupsAndSettings(): void
    {
        $groups = [
            SettingGroupType::General->value => ['General', 'Platform-wide defaults'],
            SettingGroupType::Branding->value => ['Branding', 'Company name, tagline, and brand assets'],
            SettingGroupType::Seo->value => ['SEO', 'Default search and social metadata'],
            SettingGroupType::Contact->value => ['Contact', 'Public contact details'],
            SettingGroupType::Social->value => ['Social', 'Official social profiles'],
            SettingGroupType::Email->value => ['Email', 'Outbound mail defaults'],
            SettingGroupType::Analytics->value => ['Analytics', 'Tracking identifiers'],
            SettingGroupType::Storage->value => ['Storage', 'Media and filesystem defaults'],
            SettingGroupType::Homepage->value => ['Homepage', 'Homepage copy and CTAs'],
            SettingGroupType::Footer->value => ['Footer', 'Footer copy'],
        ];

        $created = [];
        $sort = 0;

        foreach ($groups as $slug => [$name, $description]) {
            $created[$slug] = SettingGroup::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'type' => $slug,
                    'description' => $description,
                    'sort_order' => $sort++,
                ],
            );
        }

        $settings = [
            ['branding', 'company.name', 'Company name', SettingType::String, 'Zytech Contractors'],
            ['branding', 'company.short_name', 'Short name', SettingType::String, 'Zytech'],
            ['branding', 'company.tagline', 'Tagline', SettingType::String, 'Built on Kenyan soil'],
            ['branding', 'company.description', 'Company description', SettingType::Text, 'Precision-built spaces for residential and commercial clients across Nairobi, Kiambu, and nationwide — from first sketch to final handover.'],
            ['branding', 'branding.logo_url', 'Logo URL', SettingType::Url, ''],
            ['branding', 'branding.primary_color', 'Primary color', SettingType::String, '#d97706'],
            ['seo', 'seo.default_title', 'Default title', SettingType::String, 'Zytech Contractors'],
            ['seo', 'seo.default_description', 'Default description', SettingType::Text, 'Construction, architecture, and interior design across Kenya.'],
            ['seo', 'seo.keywords', 'Keywords', SettingType::String, 'construction, kenya, nairobi, architecture'],
            ['seo', 'seo.og_image', 'Open Graph image', SettingType::Url, ''],
            ['contact', 'contact.email', 'Email', SettingType::Email, 'hello@zytech.co.ke'],
            ['contact', 'contact.phone', 'Phone', SettingType::String, '+254 700 000 000'],
            ['contact', 'contact.location', 'Location', SettingType::String, 'Nairobi, Kenya'],
            ['contact', 'contact.service_area', 'Service area', SettingType::String, 'Serving Nairobi, Kiambu, and nationwide'],
            ['social', 'social.facebook', 'Facebook', SettingType::Url, ''],
            ['social', 'social.instagram', 'Instagram', SettingType::Url, ''],
            ['social', 'social.linkedin', 'LinkedIn', SettingType::Url, ''],
            ['social', 'social.x', 'X (Twitter)', SettingType::Url, ''],
            ['social', 'social.youtube', 'YouTube', SettingType::Url, ''],
            ['email', 'email.from_name', 'From name', SettingType::String, 'Zytech Contractors'],
            ['email', 'email.from_address', 'From address', SettingType::Email, 'hello@zytech.co.ke'],
            ['analytics', 'analytics.google_id', 'Google Analytics ID', SettingType::String, ''],
            ['storage', 'storage.public_disk', 'Public disk', SettingType::String, 'public'],
            ['homepage', 'homepage.hero_cta', 'Hero CTA label', SettingType::String, 'Request a Quote'],
            ['footer', 'footer.copyright', 'Copyright line', SettingType::String, 'Zytech Contractors · Nairobi, Kenya'],
            ['general', 'maintenance.enabled', 'Maintenance mode (setting)', SettingType::Boolean, '0'],
        ];

        foreach ($settings as $index => [$group, $key, $label, $type, $value]) {
            Setting::query()->updateOrCreate(
                ['key' => $key],
                [
                    'setting_group_id' => $created[$group]->id,
                    'label' => $label,
                    'type' => $type,
                    'value' => $value,
                    'is_public' => ! str_starts_with($key, 'email.') && ! str_starts_with($key, 'storage.'),
                    'sort_order' => $index,
                ],
            );
        }
    }

    private function seedFeatureFlags(): void
    {
        $flags = [
            ['maintenance_mode', 'Maintenance mode', 'Show a 503 page to visitors while staff can still access admin.', FeatureStatus::Disabled],
            ['client_portal', 'Client portal', 'Enable client portal routes and navigation.', FeatureStatus::Enabled],
            ['quotations', 'Quotations', 'Enable quotation requests from the public site.', FeatureStatus::Disabled],
            ['knowledge_centre', 'Knowledge Centre', 'Publish the Knowledge Centre section.', FeatureStatus::Disabled],
        ];

        foreach ($flags as [$key, $name, $description, $status]) {
            FeatureFlag::query()->updateOrCreate(
                ['key' => $key],
                [
                    'name' => $name,
                    'description' => $description,
                    'status' => $status,
                ],
            );
        }
    }

    private function seedNavigation(): void
    {
        $header = NavigationMenu::query()->updateOrCreate(
            ['name' => 'Primary header'],
            [
                'location' => NavigationLocation::Header,
                'is_published' => true,
            ],
        );

        $footer = NavigationMenu::query()->updateOrCreate(
            ['name' => 'Footer navigate'],
            [
                'location' => NavigationLocation::Footer,
                'is_published' => true,
            ],
        );

        $links = [
            ['Home', 'home', 0],
            ['Projects', 'projects.index', 1],
            ['Services', 'services.index', 2],
            ['About', 'about', 3],
            ['Contact', 'contact', 4],
        ];

        foreach ([$header, $footer] as $menu) {
            foreach ($links as [$label, $route, $order]) {
                NavigationItem::query()->updateOrCreate(
                    [
                        'navigation_menu_id' => $menu->id,
                        'route_name' => $route,
                    ],
                    [
                        'label' => $label,
                        'url' => null,
                        'target' => '_self',
                        'is_visible' => true,
                        'sort_order' => $order,
                    ],
                );
            }
        }
    }
}
