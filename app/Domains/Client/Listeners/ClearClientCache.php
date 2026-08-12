<?php

namespace App\Domains\Client\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Client\Events\ClientArchived;
use App\Domains\Client\Events\ClientCreated;
use App\Domains\Client\Events\ClientUpdated;
use App\Domains\Client\Events\CommunicationLogged;
use App\Domains\Client\Events\DocumentUploaded;
use App\Domains\Client\Services\ClientAnalyticsService;

final class ClearClientCache extends BaseListener
{
    public function __construct(private readonly ClientAnalyticsService $analytics) {}

    public function handle(
        ClientCreated|ClientUpdated|ClientArchived|DocumentUploaded|CommunicationLogged $event,
    ): void {
        $this->analytics->forget();
    }
}
