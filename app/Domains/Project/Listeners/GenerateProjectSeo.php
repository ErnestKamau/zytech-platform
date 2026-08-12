<?php

namespace App\Domains\Project\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Project\Events\ProjectCreated;
use App\Domains\Project\Events\ProjectUpdated;
use App\Domains\Project\Services\ProjectSEOService;
use App\Infrastructure\Queue\QueueName;

final class GenerateProjectSeo extends BaseListener
{
    public string $queue = QueueName::DEFAULT;

    public function __construct(private readonly ProjectSEOService $seo) {}

    public function handle(ProjectCreated|ProjectUpdated $event): void
    {
        $this->seo->ensure($event->project);
    }
}
