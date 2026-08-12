<?php

namespace App\Domains\Portal\Livewire;

use App\Core\Enums\QuotationStatus;
use App\Core\Livewire\BaseComponent;
use App\Domains\Portal\Exports\PortalCollectionExport;
use App\Domains\Portal\Livewire\Concerns\ResolvesPortalClient;
use App\Domains\Portal\Services\PortalService;
use App\Domains\Quotation\Services\QuotationService;
use App\Models\Quotation;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

#[Layout('layouts.portal')]
#[Title('Quotations')]
final class Quotations extends BaseComponent
{
    use ResolvesPortalClient;

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = '';

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

    public function export(PortalService $portal): BinaryFileResponse
    {
        $rows = $this->filtered($portal)->map(fn (Quotation $quotation) => [
            'reference' => $quotation->reference_number,
            'title' => $quotation->title,
            'status' => $quotation->status->label(),
            'total' => $quotation->total_amount,
            'valid_until' => optional($quotation->valid_until)->toDateString(),
        ]);

        return Excel::download(
            new PortalCollectionExport($rows, ['Reference', 'Title', 'Status', 'Total', 'Valid until']),
            'portal-quotations.xlsx',
        );
    }

    public function render(PortalService $portal): View
    {
        $statusOptions = ['' => 'All statuses'] + collect(QuotationStatus::cases())
            ->mapWithKeys(fn (QuotationStatus $status) => [$status->value => $status->label()])
            ->all();

        return view('livewire.portal.quotations', [
            'quotations' => $this->filtered($portal),
            'statusOptions' => $statusOptions,
        ]);
    }

    private function filtered(PortalService $portal)
    {
        return $portal->quotations($this->portalClient())
            ->when($this->search !== '', function ($collection) {
                $needle = mb_strtolower($this->search);

                return $collection->filter(function (Quotation $quotation) use ($needle) {
                    return str_contains(mb_strtolower((string) $quotation->reference_number), $needle)
                        || str_contains(mb_strtolower((string) $quotation->title), $needle);
                });
            })
            ->when($this->status !== '', fn ($collection) => $collection->filter(
                fn (Quotation $quotation) => $quotation->status->value === $this->status
            ))
            ->values();
    }

    private function findOwned(string $id, PortalService $portal): Quotation
    {
        $quotation = $portal->quotations($this->portalClient())->firstWhere('id', $id);
        abort_unless($quotation instanceof Quotation, 404);

        return $quotation;
    }
}
