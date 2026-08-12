<?php

namespace App\Domains\Communication\Events;

use App\Core\Events\BusinessEvent;

final class NotificationDispatched extends BusinessEvent
{
    public function __construct(
        public string $type,
        public string $recipient,
        public string $subject,
    ) {}
}
