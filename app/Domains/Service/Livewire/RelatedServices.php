<?php

namespace App\Domains\Service\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Service\Services\ServiceService;
use App\Models\Service;
use Illuminate\Contracts\View\View;

final class RelatedServices extends BaseComponent
{
    public string $serviceId;

    public function render(): View
    {
        $service = Service::query()->find($this->serviceId);

        return view('livewire.service.related-services', [
            'services' => $service === null
                ? collect()
                : app(ServiceService::class)->related($service),
        ]);
    }
}
