<?php

namespace App\Domains\Commerce\Actions;

use App\Core\Actions\BaseAction;
use App\Core\Enums\ClientTimelineEvent;
use App\Core\Enums\ProformaInvoiceStatus;
use App\Domains\Client\Services\TimelineService;
use App\Domains\Commerce\Events\ProformaInvoiceIssued;
use App\Domains\Commerce\Services\SalesOrderService;
use App\Domains\Operations\Services\ActivityLogger;
use App\Domains\Quotation\Support\ReferenceNumber;
use App\Models\ProformaInvoice;
use App\Models\Quotation;
use Illuminate\Support\Facades\DB;

final class GenerateProformaInvoiceFromQuotation extends BaseAction
{
    public function __construct(
        private readonly SalesOrderService $orders,
        private readonly ActivityLogger $activities,
        private readonly TimelineService $timeline,
    ) {}

    public function handle(mixed ...$arguments): ProformaInvoice
    {
        /** @var Quotation $quotation */
        $quotation = $arguments[0];

        $existing = ProformaInvoice::query()
            ->where('quotation_id', $quotation->id)
            ->where('status', ProformaInvoiceStatus::Issued)
            ->first();

        if ($existing !== null) {
            return $existing;
        }

        $proforma = DB::transaction(function () use ($quotation): ProformaInvoice {
            $quotation->loadMissing(['items', 'client']);
            $salesOrder = $this->orders->findByQuotation($quotation);

            $proforma = ProformaInvoice::query()->create([
                'reference_number' => ReferenceNumber::forProforma(),
                'client_id' => $quotation->client_id,
                'quotation_id' => $quotation->id,
                'sales_order_id' => $salesOrder?->id,
                'status' => ProformaInvoiceStatus::Issued,
                'subtotal' => $quotation->subtotal,
                'tax_amount' => $quotation->tax_amount,
                'discount_amount' => $quotation->discount_amount,
                'total_amount' => $quotation->total_amount,
                'currency' => $quotation->currency ?: 'KES',
                'valid_until' => now()->addDays(14)->toDateString(),
                'payment_terms' => $quotation->terms,
                'notes' => $quotation->notes,
                'issued_at' => now(),
            ]);

            foreach ($quotation->items->where('is_optional', false)->sortBy('sort_order')->values() as $index => $item) {
                $proforma->items()->create([
                    'quotation_item_id' => $item->id,
                    'label' => $item->label,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit' => $item->unit,
                    'unit_price' => $item->unit_price,
                    'line_total' => $item->line_total,
                    'sort_order' => $index,
                ]);
            }

            $proforma = $proforma->fresh(['items']) ?? $proforma->refresh();

            $this->activities->log($proforma, 'proforma_invoice.issued', [
                'reference' => $proforma->reference_number,
                'quotation_reference' => $quotation->reference_number,
                'total_amount' => (string) $proforma->total_amount,
            ]);

            if ($quotation->client !== null) {
                $this->timeline->record(
                    $quotation->client,
                    ClientTimelineEvent::ProformaInvoiceIssued,
                    'Proforma invoice '.$proforma->reference_number.' issued',
                    number_format((float) $proforma->total_amount, 2).' '.$proforma->currency,
                    ['quotation_id' => $quotation->id, 'proforma_invoice_id' => $proforma->id],
                );
            }

            return $proforma;
        });

        event(new ProformaInvoiceIssued($proforma));

        return $proforma->refresh();
    }
}
