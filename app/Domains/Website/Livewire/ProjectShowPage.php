<?php

namespace App\Domains\Website\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Project\Services\ProjectSEOService;
use App\Domains\Project\Services\ProjectService;
use App\Domains\Service\Data\ServiceData;
use Illuminate\Contracts\View\View;

final class ProjectShowPage extends BaseComponent
{
    public string $slug;

    public function mount(string $slug): void
    {
        $this->slug = $slug;
    }

    public function render(): View
    {
        $catalogue = app(ProjectService::class);
        $project = $catalogue->findPublished($this->slug);
        $model = $catalogue->modelBySlug($this->slug);

        if ($project === null || $model === null) {
            abort(404);
        }

        $serviceCards = $model->services->map(fn ($service): ServiceData => ServiceData::fromArray([
            ...$service->toArray(),
            'category_name' => $service->category?->name ?? '',
            'category_slug' => $service->category?->slug ?? '',
        ]));

        return view('livewire.website.project-show-page', [
            'project' => $project,
            'seo' => app(ProjectSEOService::class)->forPage($project),
            'model' => $model,
            'statistics' => $model->statistics,
            'galleryItems' => $model->galleryItems,
            'beforeAfter' => $model->beforeAfter,
            'progressUpdates' => $model->progressUpdates,
            'serviceCards' => $serviceCards,
        ]);
    }
}
