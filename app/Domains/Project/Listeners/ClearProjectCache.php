<?php

namespace App\Domains\Project\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Project\Events\FeaturedProjectChanged;
use App\Domains\Project\Events\ProjectArchived;
use App\Domains\Project\Events\ProjectCreated;
use App\Domains\Project\Events\ProjectPublished;
use App\Domains\Project\Events\ProjectUpdated;
use App\Domains\Project\Services\ProjectService;

final class ClearProjectCache extends BaseListener
{
    public function __construct(private readonly ProjectService $projects) {}

    public function handle(
        ProjectCreated|ProjectPublished|ProjectUpdated|ProjectArchived|FeaturedProjectChanged $event,
    ): void {
        $this->projects->forget($event->project->slug);
    }
}
