<?php

namespace App\Domains\Commerce\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Commerce\Events\ProformaInvoiceIssued;
use App\Domains\Commerce\Services\ProformaInvoicePDFService;
use App\Infrastructure\Queue\QueueName;
use Illuminate\Support\Facades\Log;

final class GenerateProformaInvoicePdf extends BaseListener
{
    public string $queue = QueueName::DEFAULT;

    public function __construct(private readonly ProformaInvoicePDFService $pdf) {}

    public function handle(ProformaInvoiceIssued $event): void
    {
        $document = $this->pdf->generate($event->proformaInvoice);

        Log::info('proforma_invoice.pdf.generated', [
            'proforma_invoice_id' => $event->proformaInvoice->getKey(),
            'document_id' => $document->getKey(),
            'verification_code' => $document->verification_code,
        ]);
    }
}
