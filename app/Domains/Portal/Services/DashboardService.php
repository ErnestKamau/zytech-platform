<?php

namespace App\Domains\Portal\Services;

use App\Core\Contracts\CacheStore;
use App\Core\Enums\ConstructionStage;
use App\Core\Enums\DocumentVisibility;
use App\Core\Enums\InvoiceStatus;
use App\Core\Enums\OrderStatus;
use App\Core\Enums\PaymentStatus;
use App\Core\Enums\ProjectStatus;
use App\Core\Enums\QuotationStatus;
use App\Core\Services\BaseService;
use App\Domains\Commerce\Services\FinanceAnalyticsService;
use App\Domains\Portal\Data\DashboardData;
use App\Domains\Portal\Repositories\MeetingRepository;
use App\Domains\Portal\Support\PortalCache;
use App\Models\Client;
use App\Models\ClientTimeline;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PortalAnnouncement;
use App\Models\PortalConversation;
use App\Models\PortalMessage;
use App\Models\PortalNotification;
use App\Models\Project;
use App\Models\Quotation;
use App\Support\Helpers\MoneyFormatter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

final class DashboardService extends BaseService
{
    public function __construct(
        private readonly CacheStore $cache,
        private readonly MeetingRepository $meetings,
        private readonly ClientActionItemsService $actionItems,
    ) {}

    public function forClient(Client $client): DashboardData
    {
        $key = PortalCache::dashboardKey($client->id);
        $cached = $this->cache->get($key);

        if (is_array($cached)) {
            return DashboardData::fromArray($cached);
        }

        $reviewStatuses = [QuotationStatus::Sent, QuotationStatus::Viewed];
        $openInvoiceStatuses = FinanceAnalyticsService::openInvoiceStatuses();

        $pendingQuotations = $client->quotations()
            ->whereIn('status', $reviewStatuses)
            ->count();

        $activeProjects = $this->activeProjectsQuery($client)->count();

        $outstanding = (float) $client->invoices()
            ->whereIn('status', $openInvoiceStatuses)
            ->sum('amount_due');

        $openInvoices = $client->invoices()
            ->whereIn('status', $openInvoiceStatuses)
            ->count();

        $overdueCount = $client->invoices()
            ->where('status', InvoiceStatus::Overdue)
            ->count();

        $paidThisYear = (float) Payment::query()
            ->where('status', PaymentStatus::Completed)
            ->where('paid_at', '>=', now()->startOfYear())
            ->where(function (Builder $query) use ($client): void {
                $query->whereHas('invoice', fn (Builder $q) => $q->where('client_id', $client->id))
                    ->orWhereHas('order', fn (Builder $q) => $q->where('client_id', $client->id));
            })
            ->sum('amount');

        $conversationIds = PortalConversation::query()
            ->where('client_id', $client->id)
            ->pluck('id');

        $unreadMessages = PortalMessage::query()
            ->whereIn('portal_conversation_id', $conversationIds)
            ->whereNull('read_at')
            ->where('user_id', '!=', $client->user_id)
            ->count();

        $unreadNotifications = PortalNotification::query()
            ->where('client_id', $client->id)
            ->whereNull('read_at')
            ->count();

        $quotations = $client->quotations()
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get(['id', 'reference_number', 'title', 'status', 'total_amount', 'currency', 'valid_until']);

        $projects = $client->projects()
            ->where('projects.status', '!=', ProjectStatus::Archived)
            ->orderByDesc('projects.updated_at')
            ->limit(5)
            ->get([
                'projects.id',
                'projects.title',
                'projects.slug',
                'projects.status',
                'projects.construction_stage',
                'projects.progress_percent',
                'projects.completed_on',
            ]);

        $orders = $client->orders()
            ->where('status', '!=', OrderStatus::Cancelled)
            ->orderByDesc('placed_at')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get(['id', 'order_number', 'status', 'total_amount', 'currency', 'placed_at']);

        $documentsQuery = $client->documents()
            ->where('visibility', DocumentVisibility::Client);
        $documentsTotal = (clone $documentsQuery)->count();
        $documentsNew = (clone $documentsQuery)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
        $latestDocuments = (clone $documentsQuery)
            ->orderByDesc('created_at')
            ->limit(3)
            ->get(['id', 'title', 'kind', 'created_at']);

        $latestConversations = PortalConversation::query()
            ->where('client_id', $client->id)
            ->with(['messages' => fn ($q) => $q->latest()->limit(1)->with('author:id,name')])
            ->orderByDesc('last_message_at')
            ->limit(3)
            ->get(['id', 'subject', 'last_message_at']);

        $activity = ClientTimeline::query()
            ->where('client_id', $client->id)
            ->orderByDesc('occurred_at')
            ->limit(8)
            ->get(['id', 'event_type', 'title', 'description', 'occurred_at']);

        $announcements = PortalAnnouncement::query()
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->limit(3)
            ->get(['id', 'title', 'body']);

        $meetings = $this->meetings->upcoming($client);

        $data = DashboardData::fromArray([
            'client_name' => $client->name,
            'pending_quotations' => $pendingQuotations,
            'active_projects' => $activeProjects,
            'outstanding' => round($outstanding, 2),
            'open_invoices' => $openInvoices,
            'unread_total' => $unreadMessages + $unreadNotifications,
            'unread_messages' => $unreadMessages,
            'unread_notifications' => $unreadNotifications,
            'action_items' => $this->actionItems->forClient($client),
            'quotations' => $quotations->map(function (Quotation $q) use ($reviewStatuses): array {
                $status = $q->status instanceof QuotationStatus ? $q->status : QuotationStatus::tryFrom((string) $q->status);

                return [
                    'id' => $q->id,
                    'reference_number' => $q->reference_number,
                    'title' => $q->title,
                    'status' => $status?->label() ?? (string) $q->status,
                    'total_amount' => (float) $q->total_amount,
                    'amount_label' => MoneyFormatter::compact((float) $q->total_amount, $q->currency ?: 'KES'),
                    'reviewable' => $status !== null && in_array($status, $reviewStatuses, true),
                    'valid_until' => $q->valid_until?->toDateString(),
                ];
            })->all(),
            'projects' => $projects->map(function (Project $p): array {
                $progress = (int) ($p->progress_percent ?? 0);

                return [
                    'id' => $p->id,
                    'title' => $p->title,
                    'slug' => $p->slug,
                    'status' => $p->statusLabel(),
                    'phase' => $p->construction_stage instanceof ConstructionStage
                        ? $p->construction_stage->label()
                        : null,
                    'progress_percent' => $progress,
                ];
            })->all(),
            'orders' => $orders->map(function (Order $order): array {
                $status = $order->status instanceof OrderStatus
                    ? $order->status
                    : OrderStatus::tryFrom((string) $order->status);

                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $status?->label() ?? (string) $order->status,
                    'total_amount' => (float) $order->total_amount,
                    'amount_label' => MoneyFormatter::compact((float) $order->total_amount, $order->currency ?: 'KES'),
                    'placed_at' => $order->placed_at?->toDateString(),
                ];
            })->all(),
            'finance' => [
                'outstanding' => round($outstanding, 2),
                'outstanding_label' => MoneyFormatter::format($outstanding),
                'open_invoices' => $openInvoices,
                'overdue' => $overdueCount,
                'paid_this_year' => round($paidThisYear, 2),
                'paid_this_year_label' => MoneyFormatter::format($paidThisYear),
            ],
            'documents' => [
                'total' => $documentsTotal,
                'new' => $documentsNew,
                'latest' => $latestDocuments->map(fn ($d): array => [
                    'id' => $d->id,
                    'title' => $d->title,
                    'kind' => $d->kind,
                    'created_at' => $d->created_at?->diffForHumans(),
                ])->all(),
            ],
            'messages' => [
                'unread' => $unreadMessages,
                'preview' => $latestConversations->map(function (PortalConversation $conversation): array {
                    $latest = $conversation->messages->first();

                    return [
                        'id' => $conversation->id,
                        'subject' => $conversation->subject,
                        'snippet' => $latest?->body
                            ? Str::limit(strip_tags((string) $latest->body), 80)
                            : null,
                        'author' => $latest?->author?->name,
                        'when' => ($conversation->last_message_at ?? $latest?->created_at)?->diffForHumans(),
                    ];
                })->all(),
            ],
            'activity' => $activity->map(fn (ClientTimeline $row): array => [
                'id' => $row->id,
                'title' => $row->title !== '' && $row->title !== null
                    ? $row->title
                    : ($row->event_type?->label() ?? 'Update'),
                'description' => $row->description,
                'when' => $row->occurred_at?->diffForHumans(),
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
        ]);

        $this->cache->put($key, $data->toArray(), now()->addMinutes(10));

        return $data;
    }

    public function forget(Client $client): void
    {
        PortalCache::forget($this->cache, $client->id);
    }

    private function activeProjectsQuery(Client $client)
    {
        return $client->projects()
            ->where('projects.status', '!=', ProjectStatus::Archived)
            ->where(function ($q): void {
                $q->whereNull('projects.construction_stage')
                    ->orWhere('projects.construction_stage', '!=', ConstructionStage::Completed);
            })
            ->whereNull('projects.completed_on');
    }
}
