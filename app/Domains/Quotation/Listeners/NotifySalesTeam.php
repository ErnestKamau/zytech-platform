<?php

namespace App\Domains\Quotation\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Quotation\Events\QuotationAccepted;
use App\Domains\Quotation\Events\QuotationApproved;
use App\Domains\Quotation\Events\QuotationCreated;
use App\Domains\Quotation\Events\QuotationRejected;
use App\Domains\Quotation\Events\QuotationRequestSubmitted;
use App\Domains\Quotation\Events\QuotationSent;
use App\Domains\Quotation\Events\SiteVisitScheduled;
use App\Infrastructure\Queue\QueueName;
use Illuminate\Support\Facades\Log;

final class NotifySalesTeam extends BaseListener
{
    public string $queue = QueueName::NOTIFICATIONS;

    public function handle(
        QuotationRequestSubmitted|QuotationCreated|QuotationApproved|QuotationSent|QuotationAccepted|QuotationRejected|SiteVisitScheduled $event,
    ): void {
        Log::info('sales.notification', [
            'event' => class_basename($event),
        ]);
    }
}
