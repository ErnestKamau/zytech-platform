<?php

namespace App\Domains\Website\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Knowledge\Services\ArticleSEOService;
use App\Domains\Knowledge\Services\KnowledgeCentreService;
use App\Domains\Project\Data\ProjectData;
use App\Domains\Service\Data\ServiceData;
use Illuminate\Contracts\View\View;

final class ArticleShowPage extends BaseComponent
{
    public string $slug;

    public function mount(string $slug): void
    {
        $this->slug = $slug;
    }

    public function render(): View
    {
        $catalogue = app(KnowledgeCentreService::class);
        $article = $catalogue->findPublished($this->slug);
        $model = $catalogue->modelBySlug($this->slug);

        if ($article === null || $model === null) {
            abort(404);
        }

        $catalogue->incrementViews($model);

        $linkedProjects = $model->projects->map(fn ($project): ProjectData => ProjectData::fromArray([
            ...$project->toArray(),
            'category_name' => $project->category?->name ?? '',
            'category_slug' => $project->category?->slug ?? '',
            'status_label' => $project->statusLabel(),
            'location_summary' => $project->locationSummary(),
        ]));

        $linkedServices = $model->services->map(fn ($service): ServiceData => ServiceData::fromArray([
            ...$service->toArray(),
            'category_name' => $service->category?->name ?? '',
            'category_slug' => $service->category?->slug ?? '',
        ]));

        return view('livewire.website.article-show-page', [
            'article' => $article,
            'seo' => app(ArticleSEOService::class)->forPage($article),
            'model' => $model,
            'downloads' => $model->downloads,
            'linkedProjects' => $linkedProjects,
            'linkedServices' => $linkedServices,
        ]);
    }
}
