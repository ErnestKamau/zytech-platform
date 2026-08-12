<?php

namespace App\Domains\Project\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Project\Events\FeaturedProjectChanged;
use App\Domains\Project\Events\ProjectArchived;
use App\Domains\Project\Events\ProjectCreated;
use App\Domains\Project\Events\ProjectPublished;
use App\Domains\Project\Events\ProjectUpdated;
use App\Infrastructure\Queue\QueueName;
use Illuminate\Support\Facades\Log;

final class BroadcastProjectChanges extends BaseListener
{
    public string $queue = QueueName::BROADCAST;

    public function handle(
        ProjectCreated|ProjectPublished|ProjectUpdated|ProjectArchived|FeaturedProjectChanged $event,
    ): void {
        Log::info('project.broadcast', [
            'event' => class_basename($event),
            'project_id' => $event->project->getKey(),
            'slug' => $event->project->slug,
        ]);
    }
}
