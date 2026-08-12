<?php

namespace App\Domains\Service\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Service\Events\FeaturedServiceChanged;
use App\Domains\Service\Events\ServiceArchived;
use App\Domains\Service\Events\ServiceCreated;
use App\Domains\Service\Events\ServicePublished;
use App\Domains\Service\Events\ServiceUpdated;
use App\Domains\Service\Services\ServiceService;

final class ClearServiceCache extends BaseListener
{
    public function __construct(
        private readonly ServiceService $services,
    ) {}

    public function handle(
        ServiceCreated|ServicePublished|ServiceUpdated|ServiceArchived|FeaturedServiceChanged $event,
    ): void {
        $this->services->forget($event->service->slug);
    }
}
