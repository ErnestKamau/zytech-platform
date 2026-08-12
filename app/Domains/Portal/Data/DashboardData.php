<?php

namespace App\Domains\Portal\Data;

use App\Core\Data\BaseDTO;

final readonly class DashboardData extends BaseDTO
{
    /**
     * @param  list<array<string, mixed>>  $quotations
     * @param  list<array<string, mixed>>  $projects
     * @param  list<array<string, mixed>>  $documents
     * @param  list<array<string, mixed>>  $notifications
     * @param  list<array<string, mixed>>  $announcements
     * @param  list<array<string, mixed>>  $meetings
     * @param  list<array<string, mixed>>  $tickets
     */
    public function __construct(
        public string $clientName,
        public int $unreadNotifications,
        public int $unreadMessages,
        public int $openTickets,
        public int $pendingQuotations,
        public array $quotations,
        public array $projects,
        public array $documents,
        public array $notifications,
        public array $announcements,
        public array $meetings,
        public array $tickets,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            clientName: (string) ($data['client_name'] ?? ''),
            unreadNotifications: (int) ($data['unread_notifications'] ?? 0),
            unreadMessages: (int) ($data['unread_messages'] ?? 0),
            openTickets: (int) ($data['open_tickets'] ?? 0),
            pendingQuotations: (int) ($data['pending_quotations'] ?? 0),
            quotations: $data['quotations'] ?? [],
            projects: $data['projects'] ?? [],
            documents: $data['documents'] ?? [],
            notifications: $data['notifications'] ?? [],
            announcements: $data['announcements'] ?? [],
            meetings: $data['meetings'] ?? [],
            tickets: $data['tickets'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'client_name' => $this->clientName,
            'unread_notifications' => $this->unreadNotifications,
            'unread_messages' => $this->unreadMessages,
            'open_tickets' => $this->openTickets,
            'pending_quotations' => $this->pendingQuotations,
            'quotations' => $this->quotations,
            'projects' => $this->projects,
            'documents' => $this->documents,
            'notifications' => $this->notifications,
            'announcements' => $this->announcements,
            'meetings' => $this->meetings,
            'tickets' => $this->tickets,
        ];
    }
}
