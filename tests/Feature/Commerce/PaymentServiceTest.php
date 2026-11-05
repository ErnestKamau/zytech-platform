<?php

namespace Tests\Feature\Commerce;

use App\Core\Enums\ClientStatus;
use App\Core\Enums\ClientType;
use App\Core\Enums\InvoiceStatus;
use App\Core\Enums\OrderPaymentStatus;
use App\Core\Enums\PaymentStatus;
use App\Domains\Commerce\Exceptions\PaymentException;
use App\Domains\Commerce\Services\PaymentService;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\SalesOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Adversarial coverage of the money-handling paths in PaymentService:
 * partial/full/overpayment, refunds, float-precision leftovers, and the
 * guard rails that stop a payment landing on a non-payable invoice.
 */
class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------
    // Invoice balance recalculation
    // -----------------------------------------------------------------

    public function test_partial_payment_marks_invoice_partially_paid_with_correct_due(): void
    {
        $invoice = $this->invoiceWithTotal('100.00');

        app(PaymentService::class)->recordForInvoice($invoice, ['amount' => 40]);

        $invoice = $invoice->fresh();
        $this->assertSame(InvoiceStatus::PartiallyPaid, $invoice->status);
        $this->assertSame('40.00', $invoice->amount_paid);
        $this->assertSame('60.00', $invoice->amount_due);
    }

    public function test_full_payment_marks_invoice_paid_with_zero_due(): void
    {
        $invoice = $this->invoiceWithTotal('100.00');

        app(PaymentService::class)->recordForInvoice($invoice, ['amount' => 100]);

        $invoice = $invoice->fresh();
        $this->assertSame(InvoiceStatus::Paid, $invoice->status);
        $this->assertSame('100.00', $invoice->amount_paid);
        $this->assertSame('0.00', $invoice->amount_due);
    }

    public function test_overpayment_clamps_amount_due_at_zero_but_keeps_true_amount_paid(): void
    {
        $invoice = $this->invoiceWithTotal('100.00');

        app(PaymentService::class)->recordForInvoice($invoice, ['amount' => 150]);

        $invoice = $invoice->fresh();
        $this->assertSame(InvoiceStatus::Paid, $invoice->status);
        $this->assertSame('150.00', $invoice->amount_paid);
        $this->assertSame('0.00', $invoice->amount_due);
    }

    public function test_multiple_payments_summing_exactly_to_total_mark_invoice_paid(): void
    {
        $invoice = $this->invoiceWithTotal('100.00');
        $service = app(PaymentService::class);

        $service->recordForInvoice($invoice, ['amount' => 33.33]);
        $service->recordForInvoice($invoice->fresh(), ['amount' => 33.33]);
        $service->recordForInvoice($invoice->fresh(), ['amount' => 33.34]);

        $invoice = $invoice->fresh();
        $this->assertSame(InvoiceStatus::Paid, $invoice->status);
        $this->assertSame('100.00', $invoice->amount_paid);
        $this->assertSame('0.00', $invoice->amount_due);
    }

    public function test_payments_leaving_a_one_cent_rounding_remainder_do_not_falsely_read_as_paid(): void
    {
        // Three equal thirds of a 100.00 invoice leave 0.01 outstanding.
        // A naive float comparison (99.99 ~= 100.00) must not round this up.
        $invoice = $this->invoiceWithTotal('100.00');
        $service = app(PaymentService::class);

        $service->recordForInvoice($invoice, ['amount' => 33.33]);
        $service->recordForInvoice($invoice->fresh(), ['amount' => 33.33]);
        $service->recordForInvoice($invoice->fresh(), ['amount' => 33.33]);

        $invoice = $invoice->fresh();
        $this->assertSame(InvoiceStatus::PartiallyPaid, $invoice->status);
        $this->assertSame('99.99', $invoice->amount_paid);
        $this->assertSame('0.01', $invoice->amount_due);
    }

    public function test_refunding_the_only_payment_reverts_a_paid_invoice_to_issued(): void
    {
        $invoice = $this->invoiceWithTotal('100.00', InvoiceStatus::Issued);
        $service = app(PaymentService::class);

        $payment = $service->recordForInvoice($invoice, ['amount' => 100]);
        $this->assertSame(InvoiceStatus::Paid, $invoice->fresh()->status);

        $service->refund($payment);

        $invoice = $invoice->fresh();
        $this->assertSame(InvoiceStatus::Issued, $invoice->status, 'Refunding the only payment must undo the Paid status, not leave it stale.');
        $this->assertSame('0.00', $invoice->amount_paid);
        $this->assertSame('100.00', $invoice->amount_due);
    }

    public function test_refunding_one_of_two_payments_keeps_invoice_partially_paid(): void
    {
        $invoice = $this->invoiceWithTotal('100.00', InvoiceStatus::Issued);
        $service = app(PaymentService::class);

        $first = $service->recordForInvoice($invoice, ['amount' => 60]);
        $service->recordForInvoice($invoice->fresh(), ['amount' => 40]);
        $this->assertSame(InvoiceStatus::Paid, $invoice->fresh()->status);

        $service->refund($first);

        $invoice = $invoice->fresh();
        $this->assertSame(InvoiceStatus::PartiallyPaid, $invoice->status);
        $this->assertSame('40.00', $invoice->amount_paid);
        $this->assertSame('60.00', $invoice->amount_due);
    }

    public function test_refunding_a_partial_payment_back_to_zero_reverts_to_issued_not_stale_partially_paid(): void
    {
        $invoice = $this->invoiceWithTotal('100.00', InvoiceStatus::Issued);
        $service = app(PaymentService::class);

        $payment = $service->recordForInvoice($invoice, ['amount' => 40]);
        $this->assertSame(InvoiceStatus::PartiallyPaid, $invoice->fresh()->status);

        $service->refund($payment);

        $invoice = $invoice->fresh();
        $this->assertSame(InvoiceStatus::Issued, $invoice->status);
        $this->assertSame('0.00', $invoice->amount_paid);
        $this->assertSame('100.00', $invoice->amount_due);
    }

    public function test_recalculating_a_never_paid_draft_invoice_does_not_force_it_to_issued(): void
    {
        $invoice = $this->invoiceWithTotal('100.00', InvoiceStatus::Draft);

        app(PaymentService::class)->recalculateInvoiceBalance($invoice);

        $this->assertSame(InvoiceStatus::Draft, $invoice->fresh()->status);
    }

    // -----------------------------------------------------------------
    // Guard rails
    // -----------------------------------------------------------------

    public function test_zero_amount_payment_is_rejected(): void
    {
        $invoice = $this->invoiceWithTotal('100.00');

        $this->expectException(PaymentException::class);
        app(PaymentService::class)->recordForInvoice($invoice, ['amount' => 0]);
    }

    public function test_negative_amount_payment_is_rejected(): void
    {
        $invoice = $this->invoiceWithTotal('100.00');

        $this->expectException(PaymentException::class);
        app(PaymentService::class)->recordForInvoice($invoice, ['amount' => -10]);
    }

    public function test_payment_without_invoice_or_order_is_rejected(): void
    {
        $this->expectException(PaymentException::class);
        app(PaymentService::class)->recordPayment(['amount' => 10]);
    }

    public function test_payment_against_a_cancelled_invoice_is_rejected(): void
    {
        $invoice = $this->invoiceWithTotal('100.00', InvoiceStatus::Cancelled);

        $this->expectException(PaymentException::class);
        app(PaymentService::class)->recordForInvoice($invoice, ['amount' => 10]);
    }

    // -----------------------------------------------------------------
    // Pending payments must not count toward the balance until completed
    // -----------------------------------------------------------------

    public function test_pending_payment_does_not_move_invoice_balance_until_marked_completed(): void
    {
        $invoice = $this->invoiceWithTotal('100.00', InvoiceStatus::Issued);
        $service = app(PaymentService::class);

        $payment = $service->recordForInvoice($invoice, [
            'amount' => 100,
            'status' => PaymentStatus::Pending,
        ]);

        $invoice = $invoice->fresh();
        $this->assertSame(InvoiceStatus::Issued, $invoice->status);
        $this->assertSame('0.00', $invoice->amount_paid);
        $this->assertSame('100.00', $invoice->amount_due);

        $service->markCompleted($payment);

        $invoice = $invoice->fresh();
        $this->assertSame(InvoiceStatus::Paid, $invoice->status);
        $this->assertSame('100.00', $invoice->amount_paid);
        $this->assertSame('0.00', $invoice->amount_due);
    }

    // -----------------------------------------------------------------
    // Direct e-commerce Order balance recalculation
    // -----------------------------------------------------------------

    public function test_order_partial_payment_marks_partially_paid(): void
    {
        $order = $this->orderWithTotal('200.00');

        app(PaymentService::class)->recordForOrder($order, ['amount' => 50]);

        $this->assertSame(OrderPaymentStatus::PartiallyPaid, $order->fresh()->payment_status);
    }

    public function test_order_full_payment_marks_paid(): void
    {
        $order = $this->orderWithTotal('200.00');

        app(PaymentService::class)->recordForOrder($order, ['amount' => 200]);

        $this->assertSame(OrderPaymentStatus::Paid, $order->fresh()->payment_status);
    }

    public function test_order_refund_of_full_payment_reverts_to_unpaid(): void
    {
        $order = $this->orderWithTotal('200.00');
        $service = app(PaymentService::class);

        $payment = $service->recordForOrder($order, ['amount' => 200]);
        $this->assertSame(OrderPaymentStatus::Paid, $order->fresh()->payment_status);

        $service->refund($payment);

        $this->assertSame(OrderPaymentStatus::Unpaid, $order->fresh()->payment_status);
    }

    public function test_multiple_small_order_payments_summing_to_total_mark_paid(): void
    {
        $order = $this->orderWithTotal('10.00');
        $service = app(PaymentService::class);

        for ($i = 0; $i < 10; $i++) {
            $service->recordForOrder($order->fresh(), ['amount' => 1]);
        }

        $this->assertSame(OrderPaymentStatus::Paid, $order->fresh()->payment_status);
    }

    private function invoiceWithTotal(string $total, InvoiceStatus $status = InvoiceStatus::Issued): Invoice
    {
        $client = Client::query()->create([
            'type' => ClientType::Individual,
            'status' => ClientStatus::Active,
            'name' => 'Payment Test Client',
            'email' => 'payment-test-'.uniqid().'@example.com',
        ]);

        $salesOrder = SalesOrder::query()->create([
            'reference_number' => 'ZSO-TEST-'.uniqid(),
            'client_id' => $client->id,
            'status' => 'confirmed',
            'subtotal' => $total,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => $total,
            'currency' => 'KES',
            'confirmed_at' => now(),
        ]);

        return Invoice::query()->create([
            'reference_number' => 'ZINV-TEST-'.uniqid(),
            'client_id' => $client->id,
            'sales_order_id' => $salesOrder->id,
            'status' => $status,
            'subtotal' => $total,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => $total,
            'amount_paid' => 0,
            'amount_due' => $total,
            'currency' => 'KES',
        ]);
    }

    private function orderWithTotal(string $total): Order
    {
        $client = Client::query()->create([
            'type' => ClientType::Individual,
            'status' => ClientStatus::Active,
            'name' => 'Order Payment Test Client',
            'email' => 'order-payment-test-'.uniqid().'@example.com',
        ]);

        return Order::query()->create([
            'order_number' => 'ZORD-TEST-'.uniqid(),
            'client_id' => $client->id,
            'status' => 'confirmed',
            'payment_status' => OrderPaymentStatus::Unpaid,
            'currency' => 'KES',
            'subtotal' => $total,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'shipping_amount' => 0,
            'total_amount' => $total,
            'placed_at' => now(),
        ]);
    }
}
