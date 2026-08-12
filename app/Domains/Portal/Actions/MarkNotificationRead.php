<?php

namespace App\Domains\Portal\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Portal\Services\NotificationService;
use App\Models\PortalNotification;

final class MarkNotificationRead extends BaseAction
{
    public function __construct(private readonly NotificationService $notifications) {}

    public function handle(mixed ...$arguments): PortalNotification
    {
        return $this->notifications->markRead($arguments[0]);
    }
}
