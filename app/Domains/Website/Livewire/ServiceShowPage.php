<?php

namespace App\Domains\Website\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Project\Data\ProjectData;
use App\Domains\Service\Services\ServiceSEOService;
use App\Domains\Service\Services\ServiceService;
use Illuminate\Contracts\View\View;

final class ServiceShowPage extends BaseComponent
{
    public string $slug;

    public function mount(string $slug): void
    {
        $this->slug = $slug;
    }

    public function render(): View
    {
        $catalogue = app(ServiceService::class);
        $service = $catalogue->findPublished($this->slug);
        $model = $catalogue->modelBySlug($this->slug);

        if ($service === null || $model === null) {
            abort(404);
        }

        $linkedProjects = $model->projects()
            ->published()
            ->public()
            ->with('category')
            ->get()
            ->map(fn ($project): ProjectData => ProjectData::fromArray([
                ...$project->toArray(),
                'category_name' => $project->category?->name ?? '',
                'category_slug' => $project->category?->slug ?? '',
                'status_label' => $project->statusLabel(),
                'location_summary' => $project->locationSummary(),
            ]));

        return view('livewire.website.service-show-page', [
            'service' => $service,
            'seo' => app(ServiceSEOService::class)->forPage($service),
            'model' => $model,
            'statistics' => $model->statistics,
            'linkedProjects' => $linkedProjects,
            'teaserProjects' => $linkedProjects->isEmpty() ? $model->relatedProjects : collect(),
        ]);
    }
}
