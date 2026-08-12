<?php

namespace App\Domains\Project\Support;

use App\Domains\Project\Services\FeaturedProjectService;
use App\Domains\Project\Services\ProjectService;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

final class ShareProjects
{
    public function __construct(
        private readonly ProjectService $projects,
        private readonly FeaturedProjectService $featured,
    ) {}

    public function compose(View $view): void
    {
        if (! $this->tablesReady()) {
            $view->with('publishedProjects', collect());
            $view->with('featuredProjects', collect());

            return;
        }

        try {
            $view->with('publishedProjects', $this->projects->published());
            $view->with('featuredProjects', $this->featured->current());
        } catch (\Throwable) {
            $view->with('publishedProjects', collect());
            $view->with('featuredProjects', collect());
        }
    }

    private function tablesReady(): bool
    {
        try {
            return Schema::hasTable('projects') && Schema::hasTable('project_categories');
        } catch (\Throwable) {
            return false;
        }
    }
}
