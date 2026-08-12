<?php

namespace App\Domains\Configuration\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Configuration\Events\BrandingUpdated;
use App\Domains\Configuration\Events\FeatureDisabled;
use App\Domains\Configuration\Events\FeatureEnabled;
use App\Domains\Configuration\Events\NavigationUpdated;
use App\Domains\Configuration\Events\SettingsUpdated;
use App\Infrastructure\Queue\QueueName;
use Illuminate\Support\Facades\Log;

final class BroadcastConfigurationChanged extends BaseListener
{
    public string $queue = QueueName::BROADCAST;

    public function handle(
        SettingsUpdated|BrandingUpdated|NavigationUpdated|FeatureEnabled|FeatureDisabled $event,
    ): void {
        Log::info('configuration.broadcast', [
            'event' => class_basename($event),
        ]);
    }
}
