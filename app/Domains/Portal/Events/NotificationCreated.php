<?php

namespace App\Domains\Portal\Events;

use App\Core\Events\BusinessEvent;
use App\Models\PortalNotification;

final class NotificationCreated extends BusinessEvent
{
    public function __construct(public PortalNotification $notification) {}
}
