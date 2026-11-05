<?php

namespace Tests\Feature\Commerce;

use App\Core\Enums\ClientStatus;
use App\Core\Enums\ClientType;
use App\Core\Enums\QuotationStatus;
use App\Domains\Commerce\Actions\GenerateProformaInvoiceFromQuotation;
use App\Domains\Commerce\Services\SalesOrderService;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\ProformaInvoice;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\SalesOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Money must survive the Quotation -> SalesOrder -> Invoice / ProformaInvoice
 * pipeline byte-for-byte: subtotal, tax, discount and total copied exactly,
 * optional line items excluded from totals, and every create-step idempotent
 * so a retried accept/webhook never double-creates or re-derives totals.
 */
class QuotationToInvoiceTotalsIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_order_copies_quotation_totals_exactly(): void
    {
        $quotation = $this->acceptedQuotationWithItems();

        $order = app(SalesOrderService::class)->createFromAcceptedQuote($quotation);

        $this->assertSame($quotation->subtotal, $order->subtotal);
        $this->assertSame($quotation->tax_amount, $order->tax_amount);
        $this->assertSame($quotation->discount_amount, $order->discount_amount);
        $this->assertSame($quotation->total_amount, $order->total_amount);
        $this->assertSame($quotation->currency, $order->currency);
    }

    public function test_draft_invoice_copies_sales_order_totals_exactly(): void
    {
        $quotation = $this->acceptedQuotationWithItems();
        $order = app(SalesOrderService::class)->createFromAcceptedQuote($quotation);

        $invoice = app(SalesOrderService::class)->createDraftInvoice($order);

        $this->assertSame($order->subtotal, $invoice->subtotal);
        $this->assertSame($order->tax_amount, $invoice->tax_amount);
        $this->assertSame($order->discount_amount, $invoice->discount_amount);
        $this->assertSame($order->total_amount, $invoice->total_amount);
        $this->assertSame('0.00', $invoice->amount_paid);
        $this->assertSame($order->total_amount, $invoice->amount_due);
    }

    public function test_proforma_invoice_copies_quotation_totals_exactly(): void
    {
        $quotation = $this->acceptedQuotationWithItems();

        $proforma = app(GenerateProformaInvoiceFromQuotation::class)->handle($quotation);

        $this->assertSame($quotation->subtotal, $proforma->subtotal);
        $this->assertSame($quotation->tax_amount, $proforma->tax_amount);
        $this->assertSame($quotation->discount_amount, $proforma->discount_amount);
        $this->assertSame($quotation->total_amount, $proforma->total_amount);
    }

    public function test_optional_items_are_excluded_from_sales_order_and_invoice_line_items(): void
    {
        $quotation = $this->acceptedQuotationWithItems();

        $order = app(SalesOrderService::class)->createFromAcceptedQuote($quotation);
        $invoice = app(SalesOrderService::class)->createDraftInvoice($order);

        $this->assertCount(2, $order->items);
        $this->assertCount(2, $invoice->items);
        $this->assertFalse($order->items->contains('label', 'Optional extra'));
        $this->assertFalse($invoice->items->contains('label', 'Optional extra'));
    }

    public function test_optional_items_are_excluded_from_proforma_line_items(): void
    {
        $quotation = $this->acceptedQuotationWithItems();

        $proforma = app(GenerateProformaInvoiceFromQuotation::class)->handle($quotation);

        $this->assertCount(2, $proforma->items);
        $this->assertFalse($proforma->items->contains('label', 'Optional extra'));
    }

    public function test_creating_sales_order_from_the_same_quotation_twice_does_not_duplicate_or_redrive_totals(): void
    {
        $quotation = $this->acceptedQuotationWithItems();
        $service = app(SalesOrderService::class);

        $first = $service->createFromAcceptedQuote($quotation);
        $second = $service->createFromAcceptedQuote($quotation->fresh());

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, SalesOrder::query()->where('quotation_id', $quotation->id)->count());
    }

    public function test_creating_draft_invoice_for_the_same_order_twice_does_not_duplicate(): void
    {
        $quotation = $this->acceptedQuotationWithItems();
        $service = app(SalesOrderService::class);
        $order = $service->createFromAcceptedQuote($quotation);

        $first = $service->createDraftInvoice($order);
        $second = $service->createDraftInvoice($order->fresh());

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, Invoice::query()->where('sales_order_id', $order->id)->count());
    }

    public function test_generating_proforma_invoice_for_the_same_quotation_twice_does_not_duplicate(): void
    {
        $quotation = $this->acceptedQuotationWithItems();
        $action = app(GenerateProformaInvoiceFromQuotation::class);

        $first = $action->handle($quotation);
        $second = $action->handle($quotation->fresh());

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, ProformaInvoice::query()->where('quotation_id', $quotation->id)->count());
    }

    public function test_tax_and_discount_bearing_quotation_still_balances_through_the_pipeline(): void
    {
        // subtotal 300.00, 16% tax = 48.00, 50.00 discount => total 298.00.
        $quotation = $this->acceptedQuotationWithItems(
            subtotal: '300.00',
            taxAmount: '48.00',
            discountAmount: '50.00',
            total: '298.00',
        );

        $order = app(SalesOrderService::class)->createFromAcceptedQuote($quotation);
        $invoice = app(SalesOrderService::class)->createDraftInvoice($order);
        $proforma = app(GenerateProformaInvoiceFromQuotation::class)->handle($quotation->fresh());

        foreach ([$order, $invoice, $proforma] as $document) {
            $this->assertSame('300.00', $document->subtotal);
            $this->assertSame('48.00', $document->tax_amount);
            $this->assertSame('50.00', $document->discount_amount);
            $this->assertSame('298.00', $document->total_amount);
        }
    }

    private function acceptedQuotationWithItems(
        string $subtotal = '200.00',
        string $taxAmount = '0.00',
        string $discountAmount = '0.00',
        string $total = '200.00',
    ): Quotation {
        $client = Client::query()->create([
            'type' => ClientType::Individual,
            'status' => ClientStatus::Active,
            'name' => 'Totals Integrity Client',
            'email' => 'totals-'.uniqid().'@example.com',
        ]);

        $quotation = Quotation::query()->create([
            'reference_number' => 'ZQ-TEST-'.uniqid(),
            'client_id' => $client->id,
            'title' => 'Totals integrity quotation',
            'status' => QuotationStatus::Accepted,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total_amount' => $total,
            'currency' => 'KES',
            'revision_number' => 1,
            'sent_at' => now(),
            'accepted_at' => now(),
        ]);

        QuotationItem::query()->create([
            'quotation_id' => $quotation->id,
            'label' => 'Core item A',
            'quantity' => 1,
            'unit_price' => 100,
            'line_total' => 100,
            'is_optional' => false,
            'sort_order' => 1,
        ]);

        QuotationItem::query()->create([
            'quotation_id' => $quotation->id,
            'label' => 'Core item B',
            'quantity' => 1,
            'unit_price' => 100,
            'line_total' => 100,
            'is_optional' => false,
            'sort_order' => 2,
        ]);

        QuotationItem::query()->create([
            'quotation_id' => $quotation->id,
            'label' => 'Optional extra',
            'quantity' => 1,
            'unit_price' => 500,
            'line_total' => 500,
            'is_optional' => true,
            'sort_order' => 3,
        ]);

        return $quotation->fresh(['items']);
    }
}
