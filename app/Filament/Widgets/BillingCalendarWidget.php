<?php

namespace App\Filament\Widgets;

use App\Core\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\ProformaInvoice;
use App\Models\SiteVisit;
use Carbon\Carbon;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class BillingCalendarWidget extends FullCalendarWidget
{
    public bool $showInvoices = true;

    public bool $showProforma = true;

    public bool $showSiteVisits = true;

    protected function headerActions(): array
    {
        return [];
    }

    protected function modalActions(): array
    {
        return [];
    }

    /**
     * @param  array{start: string, end: string, timezone: string}  $info
     */
    public function fetchEvents(array $info): array
    {
        $start = Carbon::parse($info['start']);
        $end = Carbon::parse($info['end']);
        $events = [];

        if ($this->showInvoices) {
            $events = [...$events, ...$this->invoiceEvents($start, $end)];
        }

        if ($this->showProforma) {
            $events = [...$events, ...$this->proformaEvents($start, $end)];
        }

        if ($this->showSiteVisits) {
            $events = [...$events, ...$this->siteVisitEvents($start, $end)];
        }

        return $events;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function invoiceEvents(Carbon $start, Carbon $end): array
    {
        return Invoice::query()
            ->where('status', '!=', InvoiceStatus::Draft)
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$start->toDateString(), $end->toDateString()])
            ->with('client')
            ->get()
            ->map(function (Invoice $invoice): array {
                $overdue = $invoice->status !== InvoiceStatus::Paid && $invoice->due_date->isPast();
                $dueSoon = ! $overdue && $invoice->status !== InvoiceStatus::Paid && $invoice->due_date->diffInDays(now()) <= 3;

                return [
                    'id' => 'invoice-'.$invoice->id,
                    'title' => 'Due: '.$invoice->reference_number.' ('.($invoice->client?->name ?? 'Client').')',
                    'start' => $invoice->due_date->toDateString(),
                    'color' => $overdue ? '#b42318' : ($dueSoon ? '#b45309' : '#5c7349'),
                    'url' => route('filament.admin.documents.invoice', ['invoice' => $invoice]),
                    'shouldOpenUrlInNewTab' => true,
                ];
            })
            ->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function proformaEvents(Carbon $start, Carbon $end): array
    {
        return ProformaInvoice::query()
            ->whereNotNull('valid_until')
            ->whereBetween('valid_until', [$start->toDateString(), $end->toDateString()])
            ->with('client')
            ->get()
            ->map(fn (ProformaInvoice $proforma): array => [
                'id' => 'proforma-'.$proforma->id,
                'title' => 'Expires: '.$proforma->reference_number.' ('.($proforma->client?->name ?? 'Client').')',
                'start' => $proforma->valid_until->toDateString(),
                'color' => '#2ab0df',
                'url' => route('filament.admin.documents.proforma', ['proformaInvoice' => $proforma]),
                'shouldOpenUrlInNewTab' => true,
            ])
            ->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function siteVisitEvents(Carbon $start, Carbon $end): array
    {
        return SiteVisit::query()
            ->whereNotNull('scheduled_at')
            ->whereBetween('scheduled_at', [$start, $end])
            ->get()
            ->map(fn (SiteVisit $visit): array => [
                'id' => 'site-visit-'.$visit->id,
                'title' => 'Site visit — '.($visit->location ?? 'TBC'),
                'start' => $visit->scheduled_at->toIso8601String(),
                'color' => '#8c7a68',
            ])
            ->all();
    }
}
