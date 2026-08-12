<?php

namespace App\Domains\Quotation\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Quotation\Events\QuotationAccepted;
use App\Domains\Quotation\Events\QuotationApproved;
use App\Domains\Quotation\Events\QuotationCreated;
use App\Domains\Quotation\Events\QuotationRejected;
use App\Domains\Quotation\Events\QuotationRequestSubmitted;
use App\Domains\Quotation\Events\QuotationSent;
use App\Infrastructure\Queue\QueueName;
use Illuminate\Support\Facades\Log;

final class BroadcastQuotationStatus extends BaseListener
{
    public string $queue = QueueName::BROADCAST;

    public function handle(
        QuotationRequestSubmitted|QuotationCreated|QuotationApproved|QuotationSent|QuotationAccepted|QuotationRejected $event,
    ): void {
        Log::info('quotation.broadcast', [
            'event' => class_basename($event),
        ]);
    }
}
