<?php

namespace App\Domains\Configuration\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Configuration\Events\BrandingUpdated;
use App\Domains\Configuration\Events\FeatureDisabled;
use App\Domains\Configuration\Events\FeatureEnabled;
use App\Domains\Configuration\Events\NavigationUpdated;
use App\Domains\Configuration\Events\SettingsUpdated;

final class LogConfigurationChange extends BaseListener
{
    public function handle(
        SettingsUpdated|BrandingUpdated|NavigationUpdated|FeatureEnabled|FeatureDisabled $event,
    ): void {
        $properties = match (true) {
            $event instanceof SettingsUpdated => ['keys' => $event->keys],
            $event instanceof BrandingUpdated => $event->branding->toArray(),
            $event instanceof NavigationUpdated => [
                'menu_id' => $event->menu->id,
                'location' => $event->menu->location->value,
            ],
            $event instanceof FeatureEnabled, $event instanceof FeatureDisabled => [
                'flag' => $event->flag->key,
                'status' => $event->flag->status->value,
            ],
        };

        activity('configuration')
            ->withProperties($properties)
            ->event(class_basename($event))
            ->log('Configuration changed');
    }
}
