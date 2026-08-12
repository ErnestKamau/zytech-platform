<?php

namespace App\Domains\Project\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Project\Services\ProjectService;
use App\Models\Project;
use Illuminate\Contracts\View\View;

final class RelatedProjects extends BaseComponent
{
    public string $projectId;

    public function render(): View
    {
        $project = Project::query()->find($this->projectId);

        return view('livewire.project.related-projects', [
            'projects' => $project === null
                ? collect()
                : app(ProjectService::class)->related($project),
        ]);
    }
}
