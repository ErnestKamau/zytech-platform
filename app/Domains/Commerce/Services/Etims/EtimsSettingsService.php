<?php

namespace App\Domains\Commerce\Services\Etims;

use App\Domains\Commerce\Repositories\EtimsSettingRepository;
use App\Models\EtimsSetting;

final class EtimsSettingsService
{
    public function __construct(private readonly EtimsSettingRepository $settings) {}

    public function settings(): EtimsSetting
    {
        return $this->settings->singleton();
    }

    public function isEnabled(): bool
    {
        return (bool) $this->settings()->enabled;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(array $attributes): EtimsSetting
    {
        $settings = $this->settings();
        $settings->fill($attributes)->save();

        return $settings->refresh();
    }
}
