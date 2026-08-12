<?php

namespace App\Domains\Portal\Events;

use App\Core\Events\BusinessEvent;
use App\Models\PortalMessage;

final class MessageSent extends BusinessEvent
{
    public function __construct(public PortalMessage $message) {}
}
