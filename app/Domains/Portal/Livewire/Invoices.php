<?php

namespace App\Domains\Portal\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Portal\Exports\PortalCollectionExport;
use App\Core\Enums\InvoiceStatus;
use App\Domains\Portal\Livewire\Concerns\ResolvesPortalClient;
use App\Domains\Portal\Services\PortalService;
use App\Models\Invoice;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
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

    private const STATUS_TABS = [
        'all' => ['label' => 'All', 'variant' => 'neutral'],
        'issued' => ['label' => 'Issued', 'variant' => 'info', 'status' => InvoiceStatus::Issued],
        'partially_paid' => ['label' => 'Partially paid', 'variant' => 'warning', 'status' => InvoiceStatus::PartiallyPaid],
        'paid' => ['label' => 'Paid', 'variant' => 'success', 'status' => InvoiceStatus::Paid],
        'overdue' => ['label' => 'Overdue', 'variant' => 'danger', 'status' => InvoiceStatus::Overdue],
    ];

    #[Url]
    public string $search = '';

    #[Url]
    public string $statusTab = 'all';

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
        $searched = $this->searched($portal);

        $statusTabs = collect(self::STATUS_TABS)->mapWithKeys(fn (array $tab, string $key) => [
            $key => [
                'label' => $tab['label'],
                'variant' => $tab['variant'],
                'count' => isset($tab['status'])
                    ? $searched->filter(fn (Invoice $invoice) => $invoice->status === $tab['status'])->count()
                    : $searched->count(),
            ],
        ])->all();

        return view('livewire.portal.invoices', [
            'invoices' => $this->filtered($portal),
            'statusTabs' => $statusTabs,
        ]);
    }

    private function searched(PortalService $portal): Collection
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

    private function filtered(PortalService $portal): Collection
    {
        $status = self::STATUS_TABS[$this->statusTab]['status'] ?? null;

        return $this->searched($portal)
            ->when($status !== null, fn ($collection) => $collection->filter(
                fn (Invoice $invoice) => $invoice->status === $status
            ))
            ->values();
    }
}
