<?php

namespace App\Domains\Service\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Service\Events\ServiceCreated;
use App\Domains\Service\Events\ServiceUpdated;
use App\Domains\Service\Services\ServiceSEOService;
use App\Infrastructure\Queue\QueueName;

final class GenerateServiceSeo extends BaseListener
{
    public string $queue = QueueName::DEFAULT;

    public function __construct(
        private readonly ServiceSEOService $seo,
    ) {}

    public function handle(ServiceCreated|ServiceUpdated $event): void
    {
        $this->seo->ensure($event->service);
    }
}
