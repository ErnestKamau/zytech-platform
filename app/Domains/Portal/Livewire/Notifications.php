<?php

namespace App\Domains\Portal\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Portal\Actions\MarkNotificationRead;
use App\Domains\Portal\Exports\PortalCollectionExport;
use App\Domains\Portal\Livewire\Concerns\ResolvesPortalClient;
use App\Domains\Portal\Services\NotificationService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

#[Layout('layouts.portal')]
#[Title('Notifications')]
final class Notifications extends BaseComponent
{
    use ResolvesPortalClient;

    #[Url]
    public string $search = '';

    #[Url]
    public string $read = '';

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

    public function export(NotificationService $notifications): BinaryFileResponse
    {
        $rows = $this->filtered($notifications)->map(fn ($notification) => [
            'title' => $notification->title,
            'body' => $notification->body,
            'read' => filled($notification->read_at) ? 'Read' : 'Unread',
            'created_at' => optional($notification->created_at)->toDateTimeString(),
        ]);

        return Excel::download(
            new PortalCollectionExport($rows, ['Title', 'Body', 'Read', 'Created at']),
            'portal-notifications.xlsx',
        );
    }

    public function render(NotificationService $notifications): View
    {
        return view('livewire.portal.notifications', [
            'notifications' => $this->filtered($notifications),
            'readOptions' => [
                '' => 'All',
                'unread' => 'Unread',
                'read' => 'Read',
            ],
        ]);
    }

    private function filtered(NotificationService $notifications)
    {
        return $notifications->forClient($this->portalClient())
            ->when($this->search !== '', function ($collection) {
                $needle = mb_strtolower($this->search);

                return $collection->filter(function ($notification) use ($needle) {
                    return str_contains(mb_strtolower((string) $notification->title), $needle)
                        || str_contains(mb_strtolower((string) $notification->body), $needle);
                });
            })
            ->when($this->read === 'unread', fn ($collection) => $collection->filter(fn ($n) => blank($n->read_at)))
            ->when($this->read === 'read', fn ($collection) => $collection->filter(fn ($n) => filled($n->read_at)))
            ->values();
    }
}
