<?php

namespace App\Domains\Quotation\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Quotation\Events\QuotationRequestSubmitted;
use App\Domains\Quotation\Events\QuotationSent;
use App\Infrastructure\Queue\QueueName;
use Illuminate\Support\Facades\Log;

final class SendQuotationEmail extends BaseListener
{
    public string $queue = QueueName::MAIL;

    public function handle(QuotationRequestSubmitted|QuotationSent $event): void
    {
        Log::info('quotation.email.queued', [
            'event' => class_basename($event),
        ]);
    }
}
