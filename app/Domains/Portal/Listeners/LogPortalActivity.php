<?php

namespace App\Domains\Portal\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Portal\Events\ClientLoggedIn;
use App\Domains\Portal\Events\MeetingCancelled;
use App\Domains\Portal\Events\MeetingScheduled;
use App\Domains\Portal\Events\MessageSent;
use App\Domains\Portal\Events\PortalDocumentDownloaded;
use App\Domains\Portal\Events\TicketClosed;
use App\Domains\Portal\Events\TicketOpened;
use Illuminate\Support\Facades\Log;

final class LogPortalActivity extends BaseListener
{
    public function handle(
        ClientLoggedIn|MessageSent|TicketOpened|TicketClosed|MeetingScheduled|MeetingCancelled|PortalDocumentDownloaded $event,
    ): void {
        Log::info('portal.activity', ['event' => class_basename($event)]);
    }
}
