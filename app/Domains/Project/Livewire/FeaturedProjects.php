<?php

namespace App\Domains\Project\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Project\Services\FeaturedProjectService;
use Illuminate\Contracts\View\View;

final class FeaturedProjects extends BaseComponent
{
    public function render(): View
    {
        return view('livewire.project.featured-projects', [
            'projects' => app(FeaturedProjectService::class)->current(),
        ]);
    }
}
