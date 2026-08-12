<?php

namespace Tests\Feature\Configuration;

use App\Core\Enums\FeatureStatus;
use App\Core\Enums\NavigationLocation;
use App\Core\Enums\RoleType;
use App\Core\Enums\SettingGroupType;
use App\Core\Enums\SettingType;
use App\Core\Enums\UserType;
use App\Domains\Configuration\Actions\DisableFeature;
use App\Domains\Configuration\Actions\EnableFeature;
use App\Domains\Configuration\Actions\PublishNavigation;
use App\Domains\Configuration\Actions\UpdateBranding;
use App\Domains\Configuration\Actions\UpdateSettings;
use App\Domains\Configuration\Data\BrandingData;
use App\Domains\Configuration\Policies\SettingPolicy;
use App\Domains\Configuration\Services\ConfigurationService;
use App\Domains\Configuration\Services\FeatureFlagService;
use App\Domains\Configuration\Services\NavigationService;
use App\Domains\Configuration\Support\ConfigurationCache;
use App\Infrastructure\Cache\ApplicationCache;
use App\Models\FeatureFlag;
use App\Models\NavigationItem;
use App\Models\NavigationMenu;
use App\Models\Setting;
use App\Models\SettingGroup;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConfigurationPhase3Test extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
        $this->seedSettings();
    }

    public function test_settings_can_be_updated_and_read(): void
    {
        app(UpdateSettings::class)->handle([
            'contact.email' => 'sales@zytech.co.ke',
        ]);

        $this->assertSame('sales@zytech.co.ke', app(ConfigurationService::class)->get('contact.email'));
    }

    public function test_settings_cache_is_invalidated_on_update(): void
    {
        $cache = app(ApplicationCache::class);
        $cache->put(ConfigurationCache::SETTINGS_ALL, ['contact.email' => 'stale@example.com'], 60);

        app(UpdateSettings::class)->handle([
            'contact.email' => 'fresh@zytech.co.ke',
        ]);

        $this->assertNull($cache->get(ConfigurationCache::SETTINGS_ALL));
        $this->assertSame('fresh@zytech.co.ke', app(ConfigurationService::class)->get('contact.email'));
    }

    public function test_branding_can_be_updated(): void
    {
        $branding = app(UpdateBranding::class)->handle(BrandingData::fromArray([
            'company.name' => 'Zytech Built',
            'company.short_name' => 'Zytech',
            'company.tagline' => 'Built to last',
            'company.description' => 'Kenyan construction.',
            'branding.logo_url' => '',
            'branding.primary_color' => '#111111',
        ]));

        $this->assertSame('Zytech Built', $branding->companyName);
        $this->assertSame('Built to last', app(ConfigurationService::class)->branding()->tagline);
    }

    public function test_feature_flags_can_be_toggled(): void
    {
        $this->assertFalse(app(FeatureFlagService::class)->enabled('quotations'));

        app(EnableFeature::class)->handle('quotations');
        $this->assertTrue(app(FeatureFlagService::class)->enabled('quotations'));

        app(DisableFeature::class)->handle('quotations');
        $this->assertFalse(app(FeatureFlagService::class)->enabled('quotations'));
    }

    public function test_navigation_can_be_published_for_a_location(): void
    {
        $draft = NavigationMenu::query()->create([
            'name' => 'Alt header',
            'location' => NavigationLocation::Header,
            'is_published' => false,
        ]);

        NavigationItem::query()->create([
            'navigation_menu_id' => $draft->id,
            'label' => 'Home',
            'route_name' => 'home',
            'is_visible' => true,
            'sort_order' => 0,
            'target' => '_self',
        ]);

        app(PublishNavigation::class)->handle($draft);

        $published = app(NavigationService::class)->published(NavigationLocation::Header);

        $this->assertNotNull($published);
        $this->assertSame('Alt header', $published->name);
        $this->assertSame('Home', $published->items[0]['label']);
        $this->assertFalse(NavigationMenu::query()->where('name', 'Primary header')->first()->is_published);
    }

    public function test_setting_policy_denies_clients(): void
    {
        $client = User::factory()->create(['type' => UserType::Client]);
        $client->assignRole(RoleType::Client->value);
        $setting = Setting::query()->first();
        $policy = new SettingPolicy;

        $this->assertFalse($policy->update($client, $setting));
        $this->assertFalse($policy->viewAny($client));
    }

    public function test_super_admin_can_manage_settings(): void
    {
        $admin = User::factory()->administrator()->create();
        $admin->assignRole(RoleType::SuperAdmin->value);
        $setting = Setting::query()->first();
        $policy = new SettingPolicy;

        $this->assertTrue($policy->update($admin, $setting));
    }

    public function test_home_page_renders_configured_branding(): void
    {
        app(UpdateSettings::class)->handle([
            'company.short_name' => 'Zytech',
            'company.tagline' => 'Configured tagline',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Configured tagline');
    }

    private function seedSettings(): void
    {
        $branding = SettingGroup::query()->create([
            'name' => 'Branding',
            'slug' => 'branding',
            'type' => SettingGroupType::Branding,
            'sort_order' => 0,
        ]);
        $contact = SettingGroup::query()->create([
            'name' => 'Contact',
            'slug' => 'contact',
            'type' => SettingGroupType::Contact,
            'sort_order' => 1,
        ]);

        foreach ([
            [$branding, 'company.name', 'Company name', 'Zytech Contractors'],
            [$branding, 'company.short_name', 'Short name', 'Zytech'],
            [$branding, 'company.tagline', 'Tagline', 'Built on Kenyan soil'],
            [$branding, 'company.description', 'Description', 'Precision-built spaces.'],
            [$branding, 'branding.logo_url', 'Logo', ''],
            [$branding, 'branding.primary_color', 'Color', '#d97706'],
            [$contact, 'contact.email', 'Email', 'hello@zytech.co.ke'],
            [$contact, 'contact.phone', 'Phone', '+254 700 000 000'],
            [$contact, 'contact.location', 'Location', 'Nairobi, Kenya'],
            [$contact, 'contact.service_area', 'Area', 'Nationwide'],
        ] as $index => [$group, $key, $label, $value]) {
            Setting::query()->create([
                'setting_group_id' => $group->id,
                'key' => $key,
                'label' => $label,
                'type' => SettingType::String,
                'value' => $value,
                'is_public' => true,
                'sort_order' => $index,
            ]);
        }

        FeatureFlag::query()->create([
            'key' => 'quotations',
            'name' => 'Quotations',
            'status' => FeatureStatus::Disabled,
        ]);

        $menu = NavigationMenu::query()->create([
            'name' => 'Primary header',
            'location' => NavigationLocation::Header,
            'is_published' => true,
        ]);

        NavigationItem::query()->create([
            'navigation_menu_id' => $menu->id,
            'label' => 'Home',
            'route_name' => 'home',
            'is_visible' => true,
            'sort_order' => 0,
            'target' => '_self',
        ]);
    }
}
