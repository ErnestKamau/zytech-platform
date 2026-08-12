<?php

namespace App\Domains\Portal\Services;

use App\Core\Contracts\CacheStore;
use App\Core\Enums\DocumentVisibility;
use App\Core\Enums\QuotationStatus;
use App\Core\Enums\TicketStatus;
use App\Core\Services\BaseService;
use App\Domains\Portal\Data\DashboardData;
use App\Domains\Portal\Repositories\MeetingRepository;
use App\Domains\Portal\Support\PortalCache;
use App\Models\Client;
use App\Models\PortalAnnouncement;
use App\Models\PortalConversation;
use App\Models\PortalMessage;
use App\Models\PortalNotification;
use App\Models\SupportTicket;

final class DashboardService extends BaseService
{
    public function __construct(
        private readonly CacheStore $cache,
        private readonly MeetingRepository $meetings,
    ) {}

    public function forClient(Client $client): DashboardData
    {
        $key = PortalCache::dashboardKey($client->id);
        $cached = $this->cache->get($key);

        if (is_array($cached)) {
            return DashboardData::fromArray($cached);
        }

        $quotations = $client->quotations()
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get(['id', 'reference_number', 'title', 'status', 'total_amount', 'valid_until']);

        $projects = $client->projects()
            ->orderBy('title')
            ->limit(5)
            ->get(['projects.id', 'projects.title', 'projects.slug', 'projects.status']);

        $documents = $client->documents()
            ->where('visibility', DocumentVisibility::Client)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get(['id', 'title', 'kind', 'created_at']);

        $notifications = PortalNotification::query()
            ->where('client_id', $client->id)
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        $announcements = PortalAnnouncement::query()
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        $meetings = $this->meetings->upcoming($client);
        $tickets = SupportTicket::query()
            ->where('client_id', $client->id)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get(['id', 'reference_number', 'subject', 'status']);

        $conversationIds = PortalConversation::query()
            ->where('client_id', $client->id)
            ->pluck('id');

        $unreadMessages = PortalMessage::query()
            ->whereIn('portal_conversation_id', $conversationIds)
            ->whereNull('read_at')
            ->where('user_id', '!=', $client->user_id)
            ->count();

        $data = DashboardData::fromArray([
            'client_name' => $client->name,
            'unread_notifications' => PortalNotification::query()
                ->where('client_id', $client->id)
                ->whereNull('read_at')
                ->count(),
            'unread_messages' => $unreadMessages,
            'open_tickets' => SupportTicket::query()
                ->where('client_id', $client->id)
                ->whereIn('status', [TicketStatus::Open, TicketStatus::InProgress, TicketStatus::Waiting])
                ->count(),
            'pending_quotations' => $client->quotations()
                ->whereIn('status', [QuotationStatus::Sent, QuotationStatus::Preparing, QuotationStatus::Reviewing])
                ->count(),
            'quotations' => $quotations->map(fn ($q): array => [
                'id' => $q->id,
                'reference_number' => $q->reference_number,
                'title' => $q->title,
                'status' => $q->status instanceof QuotationStatus ? $q->status->label() : (string) $q->status,
                'total_amount' => $q->total_amount,
                'valid_until' => $q->valid_until?->toDateString(),
            ])->all(),
            'projects' => $projects->map(fn ($p): array => [
                'id' => $p->id,
                'title' => $p->title,
                'slug' => $p->slug,
                'status' => is_object($p->status) && method_exists($p->status, 'label')
                    ? $p->status->label()
                    : (string) $p->status,
            ])->all(),
            'documents' => $documents->map(fn ($d): array => [
                'id' => $d->id,
                'title' => $d->title,
                'kind' => $d->kind,
                'created_at' => $d->created_at?->toDateString(),
            ])->all(),
            'notifications' => $notifications->map(fn ($n): array => [
                'id' => $n->id,
                'title' => $n->title,
                'body' => $n->body,
                'type' => $n->type?->value,
                'read' => $n->read_at !== null,
                'created_at' => $n->created_at?->diffForHumans(),
            ])->all(),
            'announcements' => $announcements->map(fn ($a): array => [
                'id' => $a->id,
                'title' => $a->title,
                'body' => $a->body,
            ])->all(),
            'meetings' => $meetings->map(fn ($m): array => [
                'id' => $m->id,
                'type' => $m->meeting_type?->label(),
                'status' => $m->status?->label(),
                'scheduled_at' => $m->scheduled_at?->toDayDateTimeString(),
            ])->all(),
            'tickets' => $tickets->map(fn ($t): array => [
                'id' => $t->id,
                'reference_number' => $t->reference_number,
                'subject' => $t->subject,
                'status' => $t->status instanceof TicketStatus ? $t->status->label() : (string) $t->status,
            ])->all(),
        ]);

        $this->cache->put($key, $data->toArray(), now()->addMinutes(10));

        return $data;
    }

    public function forget(Client $client): void
    {
        PortalCache::forget($this->cache, $client->id);
    }
}
