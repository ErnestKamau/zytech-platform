<?php

namespace App\Domains\Portal\Livewire;

use App\Core\Enums\InvoiceStatus;
use App\Core\Livewire\BaseComponent;
use App\Domains\Portal\Livewire\Concerns\ResolvesPortalClient;
use App\Models\Invoice;
use App\Models\ProformaInvoice;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;

#[Layout('layouts.portal')]
#[Title('Billing calendar')]
final class BillingCalendar extends BaseComponent
{
    use ResolvesPortalClient;

    #[Url]
    public ?string $month = null;

    public function previousMonth(): void
    {
        $this->month = $this->cursor()->subMonthNoOverflow()->format('Y-m');
    }

    public function nextMonth(): void
    {
        $this->month = $this->cursor()->addMonthNoOverflow()->format('Y-m');
    }

    public function render(): View
    {
        $cursor = $this->cursor();
        $client = $this->portalClient();

        $invoiceEvents = Invoice::query()
            ->where('client_id', $client->id)
            ->where('status', '!=', InvoiceStatus::Draft)
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$cursor->startOfMonth()->toDateString(), $cursor->endOfMonth()->toDateString()])
            ->get()
            ->map(fn (Invoice $invoice) => [
                'date' => $invoice->due_date->toDateString(),
                'label' => 'Invoice '.$invoice->reference_number.' due',
                'type' => 'invoice',
                'url' => route('portal.invoices.view', $invoice),
            ]);

        $proformaEvents = ProformaInvoice::query()
            ->where('client_id', $client->id)
            ->whereNotNull('valid_until')
            ->whereBetween('valid_until', [$cursor->startOfMonth()->toDateString(), $cursor->endOfMonth()->toDateString()])
            ->get()
            ->map(fn (ProformaInvoice $proforma) => [
                'date' => $proforma->valid_until->toDateString(),
                'label' => 'Proforma '.$proforma->reference_number.' expires',
                'type' => 'proforma',
                'url' => route('portal.proforma.pdf', $proforma),
            ]);

        $events = $invoiceEvents->concat($proformaEvents)->groupBy('date');

        return view('livewire.portal.billing-calendar', [
            'cursor' => $cursor,
            'weeks' => $this->buildWeeks($cursor, $events),
            'events' => $events,
        ]);
    }

    private function cursor(): CarbonImmutable
    {
        return $this->month !== null
            ? CarbonImmutable::createFromFormat('Y-m', $this->month)->startOfMonth()
            : CarbonImmutable::now()->startOfMonth();
    }

    /**
     * @return list<list<array{date: CarbonImmutable, inMonth: bool, events: \Illuminate\Support\Collection}>>
     */
    private function buildWeeks(CarbonImmutable $cursor, \Illuminate\Support\Collection $events): array
    {
        $start = $cursor->startOfMonth()->startOfWeek(CarbonImmutable::MONDAY);
        $end = $cursor->endOfMonth()->endOfWeek(CarbonImmutable::SUNDAY);

        $weeks = [];
        $week = [];
        $day = $start;

        while ($day->lte($end)) {
            $week[] = [
                'date' => $day,
                'inMonth' => $day->month === $cursor->month,
                'events' => $events->get($day->toDateString(), collect()),
            ];

            if ($day->dayOfWeekIso === 7) {
                $weeks[] = $week;
                $week = [];
            }

            $day = $day->addDay();
        }

        return $weeks;
    }
}
