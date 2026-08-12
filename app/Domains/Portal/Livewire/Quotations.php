<?php

namespace App\Domains\Portal\Livewire;

use App\Core\Enums\QuotationStatus;
use App\Core\Livewire\BaseComponent;
use App\Domains\Portal\Livewire\Concerns\ResolvesPortalClient;
use App\Domains\Portal\Services\PortalService;
use App\Domains\Quotation\Services\QuotationService;
use App\Models\Quotation;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.portal')]
#[Title('Quotations')]
final class Quotations extends BaseComponent
{
    use ResolvesPortalClient;

    public function accept(string $id, QuotationService $quotations, PortalService $portal): void
    {
        $quotation = $this->findOwned($id, $portal);
        abort_unless($quotation->status === QuotationStatus::Sent, 403);
        $quotations->accept($quotation);
        session()->flash('status', 'Quotation accepted.');
    }

    public function reject(string $id, QuotationService $quotations, PortalService $portal): void
    {
        $quotation = $this->findOwned($id, $portal);
        abort_unless($quotation->status === QuotationStatus::Sent, 403);
        $quotations->reject($quotation, 'Rejected from client portal');
        session()->flash('status', 'Quotation rejected.');
    }

    public function render(PortalService $portal): View
    {
        return view('livewire.portal.quotations', [
            'quotations' => $portal->quotations($this->portalClient()),
        ]);
    }

    private function findOwned(string $id, PortalService $portal): Quotation
    {
        $quotation = $portal->quotations($this->portalClient())->firstWhere('id', $id);
        abort_unless($quotation instanceof Quotation, 404);

        return $quotation;
    }
}
