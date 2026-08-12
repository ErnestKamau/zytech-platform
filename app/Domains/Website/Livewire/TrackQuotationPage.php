<?php

namespace App\Domains\Website\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Quotation\Services\QuotationRequestService;
use Illuminate\Contracts\View\View;

final class TrackQuotationPage extends BaseComponent
{
    public string $reference = '';

    public function mount(string $reference): void
    {
        $this->reference = $reference;
    }

    public function render(): View
    {
        $request = app(QuotationRequestService::class)->findByReference($this->reference);

        if ($request === null) {
            abort(404);
        }

        return view('livewire.website.track-quotation-page', [
            'request' => $request,
        ]);
    }
}
