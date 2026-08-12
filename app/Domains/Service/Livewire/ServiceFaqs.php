<?php

namespace App\Domains\Service\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Models\ServiceFaq;
use Illuminate\Contracts\View\View;

final class ServiceFaqs extends BaseComponent
{
    public string $serviceId;

    public function render(): View
    {
        return view('livewire.service.service-faqs', [
            'faqs' => ServiceFaq::query()
                ->where('service_id', $this->serviceId)
                ->where('is_published', true)
                ->orderBy('sort_order')
                ->get(),
        ]);
    }
}
