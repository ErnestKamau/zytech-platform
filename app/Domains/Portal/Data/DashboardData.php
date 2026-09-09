<?php

namespace App\Domains\Portal\Data;

use App\Core\Data\BaseDTO;

final readonly class DashboardData extends BaseDTO
{
    /**
     * @param  list<array<string, mixed>>  $actionItems
     * @param  list<array<string, mixed>>  $quotations
     * @param  list<array<string, mixed>>  $projects
     * @param  list<array<string, mixed>>  $orders
     * @param  array<string, mixed>  $finance
     * @param  array<string, mixed>  $documents
     * @param  array<string, mixed>  $messages
     * @param  list<array<string, mixed>>  $activity
     * @param  list<array<string, mixed>>  $announcements
     * @param  list<array<string, mixed>>  $meetings
     */
    public function __construct(
        public string $clientName,
        public int $pendingQuotations,
        public int $activeProjects,
        public float $outstanding,
        public int $openInvoices,
        public int $unreadTotal,
        public int $unreadMessages,
        public int $unreadNotifications,
        public array $actionItems,
        public array $quotations,
        public array $projects,
        public array $orders,
        public array $finance,
        public array $documents,
        public array $messages,
        public array $activity,
        public array $announcements,
        public array $meetings,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            clientName: (string) ($data['client_name'] ?? ''),
            pendingQuotations: (int) ($data['pending_quotations'] ?? 0),
            activeProjects: (int) ($data['active_projects'] ?? 0),
            outstanding: (float) ($data['outstanding'] ?? 0),
            openInvoices: (int) ($data['open_invoices'] ?? 0),
            unreadTotal: (int) ($data['unread_total'] ?? 0),
            unreadMessages: (int) ($data['unread_messages'] ?? 0),
            unreadNotifications: (int) ($data['unread_notifications'] ?? 0),
            actionItems: $data['action_items'] ?? [],
            quotations: $data['quotations'] ?? [],
            projects: $data['projects'] ?? [],
            orders: $data['orders'] ?? [],
            finance: is_array($data['finance'] ?? null) ? $data['finance'] : [],
            documents: is_array($data['documents'] ?? null) ? $data['documents'] : [],
            messages: is_array($data['messages'] ?? null) ? $data['messages'] : [],
            activity: $data['activity'] ?? [],
            announcements: $data['announcements'] ?? [],
            meetings: $data['meetings'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'client_name' => $this->clientName,
            'pending_quotations' => $this->pendingQuotations,
            'active_projects' => $this->activeProjects,
            'outstanding' => $this->outstanding,
            'open_invoices' => $this->openInvoices,
            'unread_total' => $this->unreadTotal,
            'unread_messages' => $this->unreadMessages,
            'unread_notifications' => $this->unreadNotifications,
            'action_items' => $this->actionItems,
            'quotations' => $this->quotations,
            'projects' => $this->projects,
            'orders' => $this->orders,
            'finance' => $this->finance,
            'documents' => $this->documents,
            'messages' => $this->messages,
            'activity' => $this->activity,
            'announcements' => $this->announcements,
            'meetings' => $this->meetings,
        ];
    }
}
