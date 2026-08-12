<?php

namespace App\Domains\Portal\Events;

use App\Core\Events\BusinessEvent;
use App\Models\SupportTicket;

final class TicketClosed extends BusinessEvent
{
    public function __construct(public SupportTicket $ticket) {}
}
