<?php

namespace App\Domains\Portal\Services;

use App\Core\Enums\PortalNotificationType;
use App\Core\Enums\PriorityLevel;
use App\Core\Enums\TicketStatus;
use App\Core\Services\BaseService;
use App\Domains\Portal\Events\TicketClosed;
use App\Domains\Portal\Events\TicketOpened;
use App\Domains\Portal\Repositories\SupportRepository;
use App\Domains\Portal\Support\TicketReference;
use App\Models\Client;
use App\Models\SupportReply;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Support\Collection;

final class SupportService extends BaseService
{
    public function __construct(
        private readonly SupportRepository $tickets,
        private readonly NotificationService $notifications,
        private readonly DashboardService $dashboard,
    ) {}

    /**
     * @return Collection<int, SupportTicket>
     */
    public function forClient(Client $client): Collection
    {
        return $this->tickets->forClient($client);
    }

    public function open(Client $client, User $author, string $subject, string $body): SupportTicket
    {
        $ticket = SupportTicket::query()->create([
            'client_id' => $client->id,
            'reference_number' => TicketReference::next(),
            'subject' => $subject,
            'body' => $body,
            'status' => TicketStatus::Open,
            'priority' => PriorityLevel::Normal,
        ]);

        SupportReply::query()->create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $author->id,
            'body' => $body,
            'is_staff' => false,
        ]);

        $this->notifications->create(
            $client,
            PortalNotificationType::Support,
            'Support ticket opened',
            $ticket->reference_number.': '.$subject,
        );

        event(new TicketOpened($ticket->fresh(['replies'])));
        $this->dashboard->forget($client);

        return $ticket->fresh(['replies.author']);
    }

    public function reply(SupportTicket $ticket, User $author, string $body, bool $isStaff = false): SupportReply
    {
        $reply = SupportReply::query()->create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $author->id,
            'body' => $body,
            'is_staff' => $isStaff,
        ]);

        if ($ticket->status === TicketStatus::Resolved || $ticket->status === TicketStatus::Closed) {
            $ticket->forceFill(['status' => TicketStatus::Open, 'resolved_at' => null])->save();
        } elseif ($isStaff) {
            $ticket->forceFill(['status' => TicketStatus::Waiting])->save();
        } else {
            $ticket->forceFill(['status' => TicketStatus::InProgress])->save();
        }

        if ($ticket->client) {
            $this->dashboard->forget($ticket->client);
        }

        return $reply;
    }

    public function close(SupportTicket $ticket): SupportTicket
    {
        $ticket->forceFill([
            'status' => TicketStatus::Closed,
            'resolved_at' => now(),
        ])->save();

        event(new TicketClosed($ticket->refresh()));

        if ($ticket->client) {
            $this->dashboard->forget($ticket->client);
        }

        return $ticket;
    }
}
