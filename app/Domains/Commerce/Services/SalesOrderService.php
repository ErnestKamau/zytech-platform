<?php

namespace App\Domains\Commerce\Services;

use App\Core\Enums\InvoiceStatus;
use App\Core\Enums\SalesOrderStatus;
use App\Core\Services\BaseService;
use App\Domains\Commerce\Events\DraftInvoiceCreated;
use App\Domains\Commerce\Events\SalesOrderCreated;
use App\Models\Invoice;
use App\Models\PurchaseOrder;
use App\Models\Quotation;
use App\Models\SalesOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class SalesOrderService extends BaseService
{
    public function findByQuotation(Quotation $quotation): ?SalesOrder
    {
        return SalesOrder::query()
            ->where('quotation_id', $quotation->id)
            ->with(['items', 'invoice'])
            ->first();
    }

    public function createFromAcceptedQuote(Quotation $quotation, ?PurchaseOrder $purchaseOrder = null): SalesOrder
    {
        $existing = $this->findByQuotation($quotation);
        if ($existing !== null) {
            return $existing;
        }

        return DB::transaction(function () use ($quotation, $purchaseOrder): SalesOrder {
            $quotation->loadMissing(['items', 'client']);

            $order = SalesOrder::query()->create([
                'reference_number' => $this->nextReference('ZSO'),
                'client_id' => $quotation->client_id,
                'quotation_id' => $quotation->id,
                'purchase_order_id' => $purchaseOrder?->id,
                'source' => 'quote',
                'status' => SalesOrderStatus::Confirmed,
                'subtotal' => $quotation->subtotal,
                'tax_amount' => $quotation->tax_amount,
                'discount_amount' => $quotation->discount_amount,
                'total_amount' => $quotation->total_amount,
                'currency' => $quotation->currency ?: 'KES',
                'payment_terms' => $quotation->terms,
                'notes' => $quotation->notes,
                'confirmed_at' => now(),
            ]);

            foreach ($quotation->items->sortBy('sort_order')->values() as $index => $item) {
                if ($item->is_optional) {
                    continue;
                }

                $order->items()->create([
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

            event(new SalesOrderCreated($order->fresh(['items'])));

            return $order->refresh();
        });
    }

    public function createDraftInvoice(SalesOrder $order): Invoice
    {
        $existing = Invoice::query()->where('sales_order_id', $order->id)->first();
        if ($existing !== null) {
            return $existing;
        }

        return DB::transaction(function () use ($order): Invoice {
            $order->loadMissing(['items', 'quotation', 'purchaseOrder']);

            $invoice = Invoice::query()->create([
                'reference_number' => $this->nextReference('ZINV'),
                'client_id' => $order->client_id,
                'sales_order_id' => $order->id,
                'quotation_id' => $order->quotation_id,
                'purchase_order_id' => $order->purchase_order_id,
                'status' => InvoiceStatus::Draft,
                'subtotal' => $order->subtotal,
                'tax_amount' => $order->tax_amount,
                'discount_amount' => $order->discount_amount,
                'total_amount' => $order->total_amount,
                'amount_paid' => 0,
                'amount_due' => $order->total_amount,
                'currency' => $order->currency ?: 'KES',
                'payment_terms' => $order->payment_terms,
                'notes' => $order->notes,
            ]);

            foreach ($order->items as $index => $item) {
                $invoice->items()->create([
                    'sales_order_item_id' => $item->id,
                    'label' => $item->label,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit' => $item->unit,
                    'unit_price' => $item->unit_price,
                    'line_total' => $item->line_total,
                    'sort_order' => $index,
                ]);
            }

            event(new DraftInvoiceCreated($invoice->fresh(['items'])));

            return $invoice->refresh();
        });
    }

    public function issueInvoice(Invoice $invoice): Invoice
    {
        if ($invoice->status !== InvoiceStatus::Draft) {
            return $invoice;
        }

        $invoice->forceFill([
            'status' => InvoiceStatus::Issued,
            'issued_at' => now(),
            'due_date' => now()->addDays(30)->toDateString(),
        ])->save();

        return $invoice->refresh();
    }

    private function nextReference(string $prefix): string
    {
        return sprintf('%s-%s-%s', $prefix, now()->format('Ymd'), Str::upper(Str::random(5)));
    }
}
