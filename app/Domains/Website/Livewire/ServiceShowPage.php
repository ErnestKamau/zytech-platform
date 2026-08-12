<?php

namespace App\Domains\Website\Livewire;

use App\Core\Livewire\BaseComponent;
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

        return view('livewire.website.service-show-page', [
            'service' => $service,
            'seo' => app(ServiceSEOService::class)->forPage($service),
            'model' => $model,
            'statistics' => $model->statistics,
            'relatedProjects' => $model->relatedProjects,
        ]);
    }
}
