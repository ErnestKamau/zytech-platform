<?php

namespace App\Domains\Portal\Services;

use App\Core\Enums\InvoiceStatus;
use App\Core\Enums\QuotationStatus;
use App\Core\Enums\TicketStatus;
use App\Core\Services\BaseService;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\SupportTicket;
use App\Support\Helpers\MoneyFormatter;

final class ClientActionItemsService extends BaseService
{
    /**
     * Prioritized client-facing actions. Empty list means “all caught up”.
     *
     * @return list<array{
     *     key: string,
     *     severity: string,
     *     title: string,
     *     meta: string,
     *     action_label: string,
     *     url: string
     * }>
     */
    public function forClient(Client $client): array
    {
        $items = [];

        $quotes = $client->quotations()
            ->whereIn('status', [QuotationStatus::Sent, QuotationStatus::Viewed])
            ->orderByDesc('sent_at')
            ->orderByDesc('updated_at')
            ->limit(8)
            ->get(['id', 'reference_number', 'title', 'total_amount', 'currency']);

        foreach ($quotes as $quote) {
            /** @var Quotation $quote */
            $amount = MoneyFormatter::format((float) $quote->total_amount, $quote->currency ?: 'KES');
            $items[] = [
                'key' => 'quote_'.$quote->id,
                'severity' => 'warning',
                'title' => 'Quotation '.$quote->reference_number.' is waiting for your review',
                'meta' => trim($quote->title.' · '.$amount, ' ·'),
                'action_label' => 'Review quotation',
                'url' => route('portal.quotations'),
            ];
        }

        $overdue = $client->invoices()
            ->where('status', InvoiceStatus::Overdue)
            ->orderBy('due_date')
            ->limit(8)
            ->get(['id', 'reference_number', 'amount_due', 'currency', 'due_date']);

        foreach ($overdue as $invoice) {
            /** @var Invoice $invoice */
            $amount = MoneyFormatter::format((float) $invoice->amount_due, $invoice->currency ?: 'KES');
            $due = $invoice->due_date
                ? 'Due '.$invoice->due_date->diffForHumans()
                : 'Past due';
            $items[] = [
                'key' => 'invoice_'.$invoice->id,
                'severity' => 'critical',
                'title' => 'Invoice '.$invoice->reference_number.' is overdue',
                'meta' => $amount.' · '.$due,
                'action_label' => 'View invoice',
                'url' => route('portal.invoices'),
            ];
        }

        $waitingTickets = SupportTicket::query()
            ->where('client_id', $client->id)
            ->where('status', TicketStatus::Waiting)
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get(['id', 'reference_number', 'subject']);

        foreach ($waitingTickets as $ticket) {
            /** @var SupportTicket $ticket */
            $items[] = [
                'key' => 'ticket_'.$ticket->id,
                'severity' => 'info',
                'title' => 'Zytech is waiting for your reply on '.$ticket->reference_number,
                'meta' => (string) $ticket->subject,
                'action_label' => 'Open support',
                'url' => route('portal.support'),
            ];
        }

        return $items;
    }
}
