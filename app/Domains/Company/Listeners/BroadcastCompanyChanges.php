<?php

namespace App\Domains\Company\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Company\Events\BranchCreated;
use App\Domains\Company\Events\CertificationUpdated;
use App\Domains\Company\Events\CompanyUpdated;
use App\Domains\Company\Events\PartnerAdded;
use App\Domains\Company\Events\TestimonialPublished;
use App\Infrastructure\Queue\QueueName;
use Illuminate\Support\Facades\Log;

final class BroadcastCompanyChanges extends BaseListener
{
    public string $queue = QueueName::BROADCAST;

    public function handle(
        CompanyUpdated|BranchCreated|PartnerAdded|TestimonialPublished|CertificationUpdated $event,
    ): void {
        Log::info('company.broadcast', [
            'event' => class_basename($event),
        ]);
    }
}
