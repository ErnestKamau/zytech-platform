<?php

namespace App\Domains\Commerce\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Commerce\Events\InvoiceIssued;
use App\Domains\Commerce\Services\InvoicePDFService;
use App\Infrastructure\Queue\QueueName;
use Illuminate\Support\Facades\Log;

final class GenerateInvoicePdf extends BaseListener
{
    public string $queue = QueueName::DEFAULT;

    public function __construct(private readonly InvoicePDFService $pdf) {}

    public function handle(InvoiceIssued $event): void
    {
        $document = $this->pdf->generate($event->invoice);

        Log::info('invoice.pdf.generated', [
            'invoice_id' => $event->invoice->getKey(),
            'document_id' => $document->getKey(),
            'verification_code' => $document->verification_code,
        ]);
    }
}
