<?php

namespace App\Domains\Project\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Project\Events\ProjectCreated;
use App\Domains\Project\Events\ProjectPublished;
use App\Domains\Project\Events\ProjectUpdated;
use App\Infrastructure\Queue\QueueName;
use Illuminate\Support\Facades\Log;

final class IndexProject extends BaseListener
{
    public string $queue = QueueName::SEARCH;

    public function handle(ProjectCreated|ProjectPublished|ProjectUpdated $event): void
    {
        Log::info('project.indexed', [
            'project_id' => $event->project->getKey(),
            'slug' => $event->project->slug,
        ]);
    }
}
