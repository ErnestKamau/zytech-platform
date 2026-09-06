<?php

namespace App\Domains\Portal\Livewire;

use App\Core\Enums\QuotationStatus;
use App\Core\Livewire\BaseComponent;
use App\Domains\Commerce\Actions\UploadPurchaseOrder;
use App\Domains\Portal\Exports\PortalCollectionExport;
use App\Domains\Portal\Livewire\Concerns\ResolvesPortalClient;
use App\Domains\Portal\Services\PortalService;
use App\Domains\Quotation\Actions\AcceptQuote;
use App\Domains\Quotation\Actions\RequestQuoteRevision;
use App\Domains\Quotation\Services\QuotationService;
use App\Models\Quotation;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

#[Layout('layouts.portal')]
#[Title('Quotations')]
final class Quotations extends BaseComponent
{
    use ResolvesPortalClient;
    use WithFileUploads;

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = '';

    public string $revisionNotes = '';

    public string $poNumber = '';

    public string $poNotes = '';

    public ?TemporaryUploadedFile $poFile = null;

    public ?string $poQuotationId = null;

    public function accept(string $id, AcceptQuote $accept, PortalService $portal): void
    {
        $quotation = $this->findOwned($id, $portal);
        abort_unless(in_array($quotation->status, [QuotationStatus::Sent, QuotationStatus::Viewed], true), 403);
        $accept->handle($quotation);
        session()->flash('status', 'Quotation accepted. A sales order and draft invoice were created.');
    }

    public function reject(string $id, QuotationService $quotations, PortalService $portal): void
    {
        $quotation = $this->findOwned($id, $portal);
        abort_unless(in_array($quotation->status, [
            QuotationStatus::Sent,
            QuotationStatus::Viewed,
            QuotationStatus::RevisionRequested,
        ], true), 403);
        $quotations->reject($quotation, 'Rejected from client portal');
        session()->flash('status', 'Quotation rejected.');
    }

    public function requestRevision(string $id, RequestQuoteRevision $requestRevision, PortalService $portal): void
    {
        $quotation = $this->findOwned($id, $portal);
        abort_unless(in_array($quotation->status, [QuotationStatus::Sent, QuotationStatus::Viewed], true), 403);

        $this->validate([
            'revisionNotes' => ['required', 'string', 'max:5000'],
        ]);

        $requestRevision->handle($quotation, $this->revisionNotes);
        $this->revisionNotes = '';
        session()->flash('status', 'Revision requested. Our team will issue an updated quote.');
    }

    public function startPoUpload(string $id): void
    {
        $this->poQuotationId = $id;
        $this->poNumber = '';
        $this->poNotes = '';
        $this->poFile = null;
    }

    public function uploadPo(UploadPurchaseOrder $upload, PortalService $portal): void
    {
        abort_unless($this->poQuotationId !== null, 404);
        $quotation = $this->findOwned($this->poQuotationId, $portal);
        abort_unless($quotation->status === QuotationStatus::Accepted, 403);

        $this->validate([
            'poNumber' => ['required', 'string', 'max:100'],
            'poFile' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,doc,docx'],
            'poNotes' => ['nullable', 'string', 'max:2000'],
        ]);

        $upload->handle(
            $quotation,
            $this->portalClient(),
            $this->poNumber,
            $this->poFile,
            $this->poNotes ?: null,
        );

        $this->poQuotationId = null;
        $this->poFile = null;
        session()->flash('status', 'Purchase order uploaded.');
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
