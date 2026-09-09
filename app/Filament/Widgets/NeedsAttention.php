<?php

namespace App\Filament\Widgets;

use App\Core\Enums\InvoiceStatus;
use App\Core\Enums\QuotationStatus;
use App\Filament\Resources\Invoices\InvoiceResource;
use App\Filament\Resources\QuotationRequests\QuotationRequestResource;
use App\Models\Invoice;
use App\Models\QuotationRequest;
use Filament\Widgets\Widget;

class NeedsAttention extends Widget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.needs-attention';

    /**
     * @return array{
     *     requests: list<array{label: string, meta: string, url: string}>,
     *     invoices: list<array{label: string, meta: string, url: string}>
     * }
     */
    public function getItems(): array
    {
        $requests = QuotationRequest::query()
            ->where('status', QuotationStatus::Pending)
            ->orderByDesc('submitted_at')
            ->limit(6)
            ->get()
            ->map(fn (QuotationRequest $request): array => [
                'label' => $request->reference_number,
                'meta' => $request->full_name.($request->submitted_at ? ' · '.$request->submitted_at->diffForHumans() : ''),
                'url' => QuotationRequestResource::getUrl('index'),
            ])
            ->all();

        $invoices = Invoice::query()
            ->where('status', InvoiceStatus::Overdue)
            ->orderBy('due_date')
            ->limit(6)
            ->get()
            ->map(fn (Invoice $invoice): array => [
                'label' => $invoice->reference_number,
                'meta' => 'KES '.number_format((float) $invoice->amount_due, 2)
                    .($invoice->due_date ? ' · due '.$invoice->due_date->toFormattedDateString() : ''),
                'url' => InvoiceResource::getUrl('index'),
            ])
            ->all();

        return [
            'requests' => $requests,
            'invoices' => $invoices,
        ];
    }
}
