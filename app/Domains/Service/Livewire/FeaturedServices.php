<?php

namespace App\Domains\Service\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Service\Services\FeaturedServiceService;
use Illuminate\Contracts\View\View;

final class FeaturedServices extends BaseComponent
{
    public function render(): View
    {
        return view('livewire.service.featured-services', [
            'services' => app(FeaturedServiceService::class)->current(),
        ]);
    }
}
