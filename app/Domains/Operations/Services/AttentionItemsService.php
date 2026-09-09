<?php

namespace App\Domains\Operations\Services;

use App\Core\Enums\InvoiceStatus;
use App\Core\Enums\OrderStatus;
use App\Core\Enums\QuotationStatus;
use App\Core\Services\BaseService;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Quotation;
use App\Models\QuotationRequest;
use App\Support\Helpers\MoneyFormatter;
use Illuminate\Support\Carbon;

final class AttentionItemsService extends BaseService
{
    /**
     * Actionable attention categories. Empty categories are omitted.
     *
     * @return list<array{
     *     key: string,
     *     severity: string,
     *     title: string,
     *     count: int,
     *     meta: string
     * }>
     */
    public function categories(): array
    {
        $items = [];

        $overdueInvoices = Invoice::query()->where('status', InvoiceStatus::Overdue);
        $overdueCount = (clone $overdueInvoices)->count();
        if ($overdueCount > 0) {
            $overdueAmount = (float) (clone $overdueInvoices)->sum('amount_due');
            $items[] = [
                'key' => 'overdue_invoices',
                'severity' => 'danger',
                'title' => $overdueCount === 1 ? '1 overdue invoice' : "{$overdueCount} overdue invoices",
                'count' => $overdueCount,
                'meta' => MoneyFormatter::format($overdueAmount).' overdue',
            ];
        }

        $rfqActionStatuses = [
            QuotationStatus::Pending,
            QuotationStatus::Reviewing,
            QuotationStatus::Preparing,
        ];
        $rfqsAwaiting = QuotationRequest::query()->whereIn('status', $rfqActionStatuses);
        $rfqCount = (clone $rfqsAwaiting)->count();
        if ($rfqCount > 0) {
            $oldest = (clone $rfqsAwaiting)->orderBy('submitted_at')->value('submitted_at');
            $items[] = [
                'key' => 'rfqs_awaiting_action',
                'severity' => 'warning',
                'title' => $rfqCount === 1 ? '1 RFQ awaiting action' : "{$rfqCount} RFQs awaiting action",
                'count' => $rfqCount,
                'meta' => $oldest
                    ? 'Oldest: '.(Carbon::parse($oldest)->diffForHumans())
                    : 'Internal follow-up needed',
            ];
        }

        $quotesAwaiting = Quotation::query()->whereIn('status', [
            QuotationStatus::Sent,
            QuotationStatus::Viewed,
        ]);
        $quoteAwaitCount = (clone $quotesAwaiting)->count();
        if ($quoteAwaitCount > 0) {
            $quoteValue = (float) (clone $quotesAwaiting)->sum('total_amount');
            $items[] = [
                'key' => 'quotes_awaiting_response',
                'severity' => 'warning',
                'title' => $quoteAwaitCount === 1
                    ? '1 quote awaiting customer response'
                    : "{$quoteAwaitCount} quotes awaiting customer response",
                'count' => $quoteAwaitCount,
                'meta' => MoneyFormatter::format($quoteValue),
            ];
        }

        $acceptedNotInvoiced = Quotation::query()
            ->where('status', QuotationStatus::Accepted)
            ->whereDoesntHave('salesOrder.invoice')
            ->whereDoesntHave('invoices');
        $acceptedCount = (clone $acceptedNotInvoiced)->count();
        if ($acceptedCount > 0) {
            $acceptedValue = (float) (clone $acceptedNotInvoiced)->sum('total_amount');
            $items[] = [
                'key' => 'accepted_not_invoiced',
                'severity' => 'warning',
                'title' => $acceptedCount === 1
                    ? '1 accepted quote not invoiced'
                    : "{$acceptedCount} accepted quotes not invoiced",
                'count' => $acceptedCount,
                'meta' => MoneyFormatter::format($acceptedValue),
            ];
        }

        $orderCount = Order::query()->whereIn('status', [
            OrderStatus::Pending,
            OrderStatus::Confirmed,
            OrderStatus::Processing,
            OrderStatus::ReadyForFulfillment,
        ])->count();
        if ($orderCount > 0) {
            $items[] = [
                'key' => 'orders_awaiting_fulfilment',
                'severity' => 'info',
                'title' => $orderCount === 1
                    ? '1 order awaiting fulfilment'
                    : "{$orderCount} orders awaiting fulfilment",
                'count' => $orderCount,
                'meta' => 'View orders',
            ];
        }

        $unassignedRfqs = QuotationRequest::query()
            ->whereNull('assigned_to')
            ->whereIn('status', $rfqActionStatuses)
            ->count();
        if ($unassignedRfqs > 0) {
            $items[] = [
                'key' => 'unassigned_rfqs',
                'severity' => 'gray',
                'title' => $unassignedRfqs === 1
                    ? '1 unassigned RFQ'
                    : "{$unassignedRfqs} unassigned RFQs",
                'count' => $unassignedRfqs,
                'meta' => 'Requires assignment',
            ];
        }

        return $items;
    }
}
