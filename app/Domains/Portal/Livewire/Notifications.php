<?php

namespace App\Domains\Portal\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Portal\Actions\MarkNotificationRead;
use App\Domains\Portal\Livewire\Concerns\ResolvesPortalClient;
use App\Domains\Portal\Services\NotificationService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.portal')]
#[Title('Notifications')]
final class Notifications extends BaseComponent
{
    use ResolvesPortalClient;

    public function markRead(string $id, NotificationService $notifications, MarkNotificationRead $action): void
    {
        $notification = $notifications->forClient($this->portalClient())->firstWhere('id', $id);
        abort_unless($notification !== null, 404);
        $action->handle($notification);
    }

    public function markAll(NotificationService $notifications): void
    {
        $notifications->markAllRead($this->portalClient());
        session()->flash('status', 'All notifications marked as read.');
    }

    public function render(NotificationService $notifications): View
    {
        return view('livewire.portal.notifications', [
            'notifications' => $notifications->forClient($this->portalClient()),
        ]);
    }
}
