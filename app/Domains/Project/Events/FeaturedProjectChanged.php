<?php

namespace App\Domains\Project\Events;

use App\Core\Events\BusinessEvent;
use App\Models\Project;

final class FeaturedProjectChanged extends BusinessEvent
{
    public function __construct(public Project $project) {}
}
