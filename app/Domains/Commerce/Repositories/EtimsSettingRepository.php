<?php

namespace App\Domains\Commerce\Repositories;

use App\Models\EtimsSetting;

final class EtimsSettingRepository
{
    public function singleton(): EtimsSetting
    {
        return EtimsSetting::query()->firstOrCreate([], [
            'enabled' => false,
            'environment' => 'production',
        ]);
    }
}
