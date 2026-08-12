<?php

namespace App\Domains\Website\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Project\Services\FeaturedProjectService;
use App\Domains\Project\Services\ProjectMapService;
use App\Domains\Project\Services\ProjectService;
use App\Models\ProjectCategory;
use Illuminate\Contracts\View\View;

final class ProjectsPage extends BaseComponent
{
    public ?string $category = null;

    public function mount(?string $category = null): void
    {
        $this->category = $category;
    }

    public function render(): View
    {
        $projects = app(ProjectService::class);
        $selected = $this->category
            ? $projects->categories()->first(fn (ProjectCategory $cat): bool => $cat->slug === $this->category)
            : null;

        if ($this->category && $selected === null) {
            abort(404);
        }

        return view('livewire.website.projects-page', [
            'selectedCategory' => $selected,
            'categories' => $projects->categories(),
            'projects' => $projects->published($this->category),
            'featured' => app(FeaturedProjectService::class)->current(),
            'mapMarkers' => app(ProjectMapService::class)->markers(),
        ]);
    }
}
