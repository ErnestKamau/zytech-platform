<?php

namespace App\Domains\Portal\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Portal\Exports\PortalCollectionExport;
use App\Domains\Portal\Livewire\Concerns\ResolvesPortalClient;
use App\Domains\Portal\Services\PortalService;
use App\Models\Invoice;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

#[Layout('layouts.portal')]
#[Title('Invoices')]
final class Invoices extends BaseComponent
{
    use ResolvesPortalClient;

    #[Url]
    public string $search = '';

    public function export(PortalService $portal): BinaryFileResponse
    {
        $rows = $this->filtered($portal)->map(fn (Invoice $invoice) => [
            'reference' => $invoice->reference_number,
            'status' => $invoice->status->label(),
            'total' => $invoice->total_amount,
            'amount_due' => $invoice->amount_due,
            'order' => $invoice->salesOrder?->reference_number,
        ]);

        return Excel::download(
            new PortalCollectionExport($rows, ['Reference', 'Status', 'Total', 'Amount due', 'Order']),
            'portal-invoices.xlsx',
        );
    }

    public function render(PortalService $portal): View
    {
        return view('livewire.portal.invoices', [
            'invoices' => $this->filtered($portal),
        ]);
    }

    private function filtered(PortalService $portal)
    {
        return $portal->invoices($this->portalClient())
            ->when($this->search !== '', function ($collection) {
                $needle = mb_strtolower($this->search);

                return $collection->filter(function (Invoice $invoice) use ($needle) {
                    return str_contains(mb_strtolower((string) $invoice->reference_number), $needle)
                        || str_contains(mb_strtolower((string) ($invoice->salesOrder?->reference_number ?? '')), $needle);
                });
            })
            ->values();
    }
}
