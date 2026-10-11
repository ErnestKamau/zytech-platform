<?php

namespace App\Domains\Commerce\Actions;

use App\Core\Actions\BaseAction;
use App\Core\Enums\InvoiceStatus;
use App\Core\Enums\InvoiceType;
use App\Domains\Commerce\Events\InvoiceIssued;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Full-reversal credit note — the sanctioned way to correct an invoice
 * that has already been issued (the original stays locked). Works whether
 * or not eTIMS is enabled: this creates a new credit_note-type Invoice row
 * pointing back at the original, then fires the same InvoiceIssued event
 * so SubmitInvoiceToEtims picks it up for KRA submission when eTIMS is on.
 */
final class IssueCreditNote extends BaseAction
{
    public function handle(mixed ...$arguments): Invoice
    {
        /** @var Invoice $invoice */
        $invoice = $arguments[0];

        if ($invoice->type === InvoiceType::CreditNote) {
            throw new RuntimeException('Cannot issue a credit note against a credit note.');
        }

        if (in_array($invoice->status, [InvoiceStatus::Draft, InvoiceStatus::Void], true)) {
            throw new RuntimeException('This invoice has not been issued; there is nothing to reverse.');
        }

        if ($invoice->creditNotes()->exists()) {
            throw new RuntimeException('A credit note has already been issued for this invoice.');
        }

        return DB::transaction(function () use ($invoice): Invoice {
            $invoice->loadMissing('items');

            $creditNote = Invoice::query()->create([
                'reference_number' => config('etims.credit_note_refno_prefix', 'CN-').$invoice->reference_number,
                'client_id' => $invoice->client_id,
                'sales_order_id' => $invoice->sales_order_id,
                'quotation_id' => $invoice->quotation_id,
                'purchase_order_id' => $invoice->purchase_order_id,
                'type' => InvoiceType::CreditNote,
                'parent_invoice_id' => $invoice->id,
                'status' => InvoiceStatus::Issued,
                'subtotal' => $invoice->subtotal,
                'tax_amount' => $invoice->tax_amount,
                'discount_amount' => $invoice->discount_amount,
                'total_amount' => $invoice->total_amount,
                'amount_paid' => 0,
                'amount_due' => 0,
                'currency' => $invoice->currency,
                'issued_at' => now(),
            ]);

            foreach ($invoice->items as $index => $item) {
                $creditNote->items()->create([
                    'product_id' => $item->product_id,
                    'label' => $item->label,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit' => $item->unit,
                    'unit_price' => $item->unit_price,
                    'line_total' => $item->line_total,
                    'sort_order' => $index,
                ]);
            }

            event(new InvoiceIssued($creditNote->fresh(['items'])));

            return $creditNote->refresh();
        });
    }
}
