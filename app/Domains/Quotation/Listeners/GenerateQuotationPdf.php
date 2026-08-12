<?php

namespace App\Domains\Quotation\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Quotation\Events\QuotationApproved;
use App\Domains\Quotation\Events\QuotationSent;
use App\Domains\Quotation\Services\QuotationPDFService;
use App\Infrastructure\Queue\QueueName;
use Illuminate\Support\Facades\Log;

final class GenerateQuotationPdf extends BaseListener
{
    public string $queue = QueueName::DEFAULT;

    public function __construct(private readonly QuotationPDFService $pdf) {}

    public function handle(QuotationApproved|QuotationSent $event): void
    {
        $document = $this->pdf->generate($event->quotation);

        Log::info('quotation.pdf.generated', [
            'quotation_id' => $event->quotation->getKey(),
            'document_id' => $document->getKey(),
            'verification_code' => $document->verification_code,
        ]);
    }
}
