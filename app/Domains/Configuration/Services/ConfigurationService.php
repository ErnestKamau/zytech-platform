<?php

namespace App\Domains\Configuration\Services;

use App\Core\Contracts\CacheStore;
use App\Core\Services\BaseService;
use App\Domains\Configuration\Data\BrandingData;
use App\Domains\Configuration\Data\SEOData;
use App\Domains\Configuration\Events\SettingsUpdated;
use App\Domains\Configuration\Repositories\SettingRepository;
use App\Domains\Configuration\Support\ConfigurationCache;

final class ConfigurationService extends BaseService
{
    public function __construct(
        private readonly SettingRepository $settings,
        private readonly CacheStore $cache,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->cache->remember(
            ConfigurationCache::SETTINGS_ALL,
            now()->addHour(),
            fn (): array => $this->settings->allKeyed(),
        );
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->all()[$key] ?? $default;
    }

    /**
     * @param  array<string, mixed>  $values
     */
    public function updateMany(array $values): void
    {
        $this->transaction(function () use ($values): void {
            foreach ($values as $key => $value) {
                $this->settings->updateValue($key, $value);
            }
        });

        $this->forgetSettingsCache();
        event(new SettingsUpdated(array_keys($values)));
    }

    public function updateValue(string $key, mixed $value): void
    {
        $this->updateMany([$key => $value]);
    }

    public function branding(): BrandingData
    {
        return $this->cache->remember(
            ConfigurationCache::BRANDING,
            now()->addHour(),
            fn (): BrandingData => BrandingData::fromArray($this->all()),
        );
    }

    public function seo(): SEOData
    {
        return $this->cache->remember(
            ConfigurationCache::SEO,
            now()->addHour(),
            fn (): SEOData => SEOData::fromArray($this->all()),
        );
    }

    /**
     * @return array{email: string, phone: string, location: string, service_area: string}
     */
    public function contact(): array
    {
        return $this->cache->remember(
            ConfigurationCache::CONTACT,
            now()->addHour(),
            fn (): array => [
                'email' => (string) $this->get('contact.email', 'hello@zytech.co.ke'),
                'phone' => (string) $this->get('contact.phone', '+254 700 000 000'),
                'location' => (string) $this->get('contact.location', 'Nairobi, Kenya'),
                'service_area' => (string) $this->get('contact.service_area', 'Serving Nairobi, Kiambu, and nationwide'),
            ],
        );
    }

    /**
     * @return array<string, string>
     */
    public function social(): array
    {
        return $this->cache->remember(
            ConfigurationCache::SOCIAL,
            now()->addHour(),
            fn (): array => [
                'facebook' => (string) $this->get('social.facebook', ''),
                'instagram' => (string) $this->get('social.instagram', ''),
                'linkedin' => (string) $this->get('social.linkedin', ''),
                'x' => (string) $this->get('social.x', ''),
                'youtube' => (string) $this->get('social.youtube', ''),
            ],
        );
    }

    public function forgetSettingsCache(): void
    {
        foreach (ConfigurationCache::settingsKeys() as $key) {
            $this->cache->forget($key);
        }
    }
}
