<?php

namespace App\Domains\Configuration\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Configuration\Actions\ClearConfigurationCache;
use App\Domains\Configuration\Events\BrandingUpdated;
use App\Domains\Configuration\Events\FeatureDisabled;
use App\Domains\Configuration\Events\FeatureEnabled;
use App\Domains\Configuration\Events\NavigationUpdated;
use App\Domains\Configuration\Events\SettingsUpdated;

final class ClearRedisConfigurationCache extends BaseListener
{
    public function __construct(
        private readonly ClearConfigurationCache $clear,
    ) {}

    public function handle(
        SettingsUpdated|BrandingUpdated|NavigationUpdated|FeatureEnabled|FeatureDisabled $event,
    ): void {
        $this->clear->handle();
    }
}
