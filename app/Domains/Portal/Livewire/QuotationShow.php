<?php

namespace App\Domains\Portal\Livewire;

use App\Core\Enums\QuotationStatus;
use App\Core\Livewire\BaseComponent;
use App\Domains\Commerce\Actions\UploadPurchaseOrder;
use App\Domains\Portal\Livewire\Concerns\ResolvesPortalClient;
use App\Domains\Portal\Services\PortalService;
use App\Domains\Quotation\Actions\AcceptQuote;
use App\Domains\Quotation\Actions\RequestQuoteRevision;
use App\Domains\Quotation\Services\QuotationService;
use App\Core\Enums\DeliveryStatus;
use App\Models\Client;
use App\Models\ClientTimeline;
use App\Models\NotificationLog;
use App\Models\ProformaInvoice;
use App\Models\Quotation;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Features\SupportFileUploads\WithFileUploads;

#[Layout('layouts.portal')]
#[Title('Quotation')]
final class QuotationShow extends BaseComponent
{
    use ResolvesPortalClient;
    use WithFileUploads;

    public string $quotationId;

    public string $activeModal = '';

    public int $acceptStep = 1;

    public string $reviewNotes = '';

    public string $poNumber = '';

    public string $poNotes = '';

    public ?TemporaryUploadedFile $poFile = null;

    public bool $showPoForm = false;

    public function mount(string $quotation): void
    {
        $this->quotationId = $quotation;
    }

    public function openModal(string $modal): void
    {
        $this->activeModal = $modal;
        $this->acceptStep = 1;
        $this->reviewNotes = '';
        $this->resetErrorBag();
    }

    public function closeModal(): void
    {
        $this->activeModal = '';
        $this->acceptStep = 1;
    }

    public function nextAcceptStep(): void
    {
        $this->acceptStep = 2;
    }

    public function previousAcceptStep(): void
    {
        $this->acceptStep = 1;
    }

    public function accept(AcceptQuote $accept, PortalService $portal): void
    {
        $quotation = $this->findOwned($portal);
        abort_unless(in_array($quotation->status, [QuotationStatus::Sent, QuotationStatus::Viewed], true), 403);

        $accept->handle($quotation);

        $this->closeModal();
        session()->flash('status', 'Quotation accepted. A proforma invoice, sales order, and draft invoice were created.');
    }

    public function reject(QuotationService $quotations, PortalService $portal): void
    {
        $quotation = $this->findOwned($portal);
        abort_unless(in_array($quotation->status, [
            QuotationStatus::Sent,
            QuotationStatus::Viewed,
            QuotationStatus::RevisionRequested,
        ], true), 403);

        $quotations->reject($quotation, $this->reviewNotes ?: 'Rejected from client portal');

        $this->closeModal();
        session()->flash('status', 'Quotation rejected.');
    }

    public function requestRevision(RequestQuoteRevision $requestRevision, PortalService $portal): void
    {
        $quotation = $this->findOwned($portal);
        abort_unless(in_array($quotation->status, [QuotationStatus::Sent, QuotationStatus::Viewed], true), 403);

        $this->validate([
            'reviewNotes' => ['required', 'string', 'max:5000'],
        ]);

        $requestRevision->handle($quotation, $this->reviewNotes);

        $this->closeModal();
        session()->flash('status', 'Revision requested. Our team will issue an updated quote.');
    }

    public function uploadPo(UploadPurchaseOrder $upload, PortalService $portal): void
    {
        $quotation = $this->findOwned($portal);
        abort_unless($quotation->status === QuotationStatus::Accepted, 403);

        $this->validate([
            'poNumber' => ['required', 'string', 'max:100'],
            'poFile' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,doc,docx'],
            'poNotes' => ['nullable', 'string', 'max:2000'],
        ]);

        $upload->handle($quotation, $this->portalClient(), $this->poNumber, $this->poFile, $this->poNotes ?: null);

        $this->showPoForm = false;
        $this->poFile = null;
        session()->flash('status', 'Purchase order uploaded.');
    }

    public function render(PortalService $portal): View
    {
        $quotation = $this->findOwned($portal);
        $quotation->loadMissing(['sections.items', 'items', 'salesOrder.invoice', 'purchaseOrder', 'proformaInvoices']);

        /** @var Client $client */
        $client = $this->portalClient();

        $timeline = ClientTimeline::query()
            ->where('client_id', $client->id)
            ->where('meta->quotation_id', $quotation->id)
            ->orderByDesc('occurred_at')
            ->limit(30)
            ->get();

        $proforma = $quotation->proformaInvoices->first();

        $deliveredChannels = NotificationLog::query()
            ->where('meta->quotation_id', $quotation->id)
            ->where('status', DeliveryStatus::Sent)
            ->orderByDesc('created_at')
            ->get()
            ->unique('channel');

        return view('livewire.portal.quotation-show', [
            'quotation' => $quotation,
            'timeline' => $timeline,
            'proforma' => $proforma instanceof ProformaInvoice ? $proforma : null,
            'deliveredChannels' => $deliveredChannels,
            'reviewable' => $quotation->isSharedWithClient() && in_array($quotation->status, [
                QuotationStatus::Sent,
                QuotationStatus::Viewed,
            ], true),
        ]);
    }

    private function findOwned(PortalService $portal): Quotation
    {
        $quotation = $portal->quotations($this->portalClient())->firstWhere('id', $this->quotationId);
        abort_unless($quotation instanceof Quotation, 404);

        return $quotation;
    }
}
