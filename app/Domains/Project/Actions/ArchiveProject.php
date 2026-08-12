<?php

namespace App\Domains\Project\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Project\Services\ProjectService;
use App\Models\Project;

final class ArchiveProject extends BaseAction
{
    public function __construct(private readonly ProjectService $projects) {}

    public function handle(mixed ...$arguments): Project
    {
        /** @var Project $project */
        $project = $arguments[0];

        return $this->projects->archive($project);
    }
}
