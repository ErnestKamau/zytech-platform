<?php

namespace App\Domains\Project\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Project\Services\ProjectService;
use App\Models\Project;

final class FeatureProject extends BaseAction
{
    public function __construct(private readonly ProjectService $projects) {}

    public function handle(mixed ...$arguments): Project
    {
        /** @var Project $project */
        $project = $arguments[0];
        $featured = (bool) ($arguments[1] ?? true);

        return $this->projects->feature($project, $featured);
    }
}
