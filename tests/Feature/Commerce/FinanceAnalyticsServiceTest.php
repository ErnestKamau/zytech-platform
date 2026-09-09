<?php

namespace Tests\Feature\Commerce;

use App\Core\Enums\ClientStatus;
use App\Core\Enums\ClientType;
use App\Core\Enums\InvoiceStatus;
use App\Core\Enums\OrderStatus;
use App\Core\Enums\PaymentStatus;
use App\Domains\Commerce\Services\FinanceAnalyticsService;
use App\Domains\Commerce\Services\OrderMetricsService;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\SalesOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceAnalyticsServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_snapshot_aggregates_revenue_and_invoice_status(): void
    {
        $client = Client::query()->create([
            'type' => ClientType::Individual,
            'status' => ClientStatus::Active,
            'name' => 'Finance Client',
            'email' => 'finance-client@example.com',
        ]);

        $salesOrder = SalesOrder::query()->create([
            'reference_number' => 'SO-FIN-001',
            'client_id' => $client->id,
            'status' => 'confirmed',
            'total_amount' => 10000,
        ]);

        $paidInvoice = Invoice::query()->create([
            'reference_number' => 'INV-PAID-001',
            'client_id' => $client->id,
            'sales_order_id' => $salesOrder->id,
            'status' => InvoiceStatus::Paid,
            'total_amount' => 5000,
            'amount_paid' => 5000,
            'amount_due' => 0,
        ]);

        $overdueInvoice = Invoice::query()->create([
            'reference_number' => 'INV-OD-001',
            'client_id' => $client->id,
            'sales_order_id' => $salesOrder->id,
            'status' => InvoiceStatus::Overdue,
            'total_amount' => 3000,
            'amount_paid' => 0,
            'amount_due' => 3000,
        ]);

        Invoice::query()->create([
            'reference_number' => 'INV-VOID-001',
            'client_id' => $client->id,
            'sales_order_id' => $salesOrder->id,
            'status' => InvoiceStatus::Void,
            'total_amount' => 1000,
            'amount_paid' => 0,
            'amount_due' => 1000,
        ]);

        Invoice::query()->create([
            'reference_number' => 'INV-DRAFT-001',
            'client_id' => $client->id,
            'sales_order_id' => $salesOrder->id,
            'status' => InvoiceStatus::Draft,
            'total_amount' => 9999,
            'amount_paid' => 0,
            'amount_due' => 9999,
        ]);

        Payment::query()->create([
            'invoice_id' => $paidInvoice->id,
            'amount' => 5000,
            'status' => PaymentStatus::Completed,
            'paid_at' => now(),
        ]);

        Payment::query()->create([
            'invoice_id' => $overdueInvoice->id,
            'amount' => 100,
            'status' => PaymentStatus::Pending,
            'paid_at' => null,
        ]);

        $snapshot = app(FinanceAnalyticsService::class)->snapshot();

        $this->assertSame(5000.0, $snapshot['revenue_mtd']);
        $this->assertSame(5000.0, $snapshot['revenue_last_30d']);
        $this->assertSame(3000.0, $snapshot['outstanding']);
        $this->assertSame(3000.0, $snapshot['overdue_amount']);
        $this->assertSame(1, $snapshot['invoices_paid']);
        $this->assertSame(1, $snapshot['invoices_overdue']);
        $this->assertSame(1, $snapshot['invoices_open']);
        $this->assertSame(5000.0, $snapshot['average_payment']);
    }

    public function test_revenue_series_fills_missing_days_with_zero(): void
    {
        $client = Client::query()->create([
            'type' => ClientType::Individual,
            'status' => ClientStatus::Active,
            'name' => 'Series Client',
            'email' => 'series@example.com',
        ]);

        $salesOrder = SalesOrder::query()->create([
            'reference_number' => 'SO-SERIES-001',
            'client_id' => $client->id,
            'status' => 'confirmed',
            'total_amount' => 1000,
        ]);

        $invoice = Invoice::query()->create([
            'reference_number' => 'INV-SERIES-001',
            'client_id' => $client->id,
            'sales_order_id' => $salesOrder->id,
            'status' => InvoiceStatus::Paid,
            'total_amount' => 250,
            'amount_paid' => 250,
            'amount_due' => 0,
        ]);

        Payment::query()->create([
            'invoice_id' => $invoice->id,
            'amount' => 250,
            'status' => PaymentStatus::Completed,
            'paid_at' => now(),
        ]);

        $series = app(FinanceAnalyticsService::class)->revenueSeries(7);

        $this->assertCount(7, $series['labels']);
        $this->assertCount(7, $series['values']);
        $this->assertSame(250.0, end($series['values']));
        $this->assertSame(0.0, $series['values'][0]);
    }

    public function test_order_metrics_exclude_cancelled_from_fulfilment(): void
    {
        $client = Client::query()->create([
            'type' => ClientType::Individual,
            'status' => ClientStatus::Active,
            'name' => 'Order Client',
            'email' => 'orders@example.com',
        ]);

        Order::query()->create([
            'order_number' => 'ORD-001',
            'client_id' => $client->id,
            'status' => OrderStatus::Processing,
            'payment_status' => 'unpaid',
            'total_amount' => 100,
            'placed_at' => now(),
        ]);

        Order::query()->create([
            'order_number' => 'ORD-002',
            'client_id' => $client->id,
            'status' => OrderStatus::Cancelled,
            'payment_status' => 'unpaid',
            'total_amount' => 100,
            'placed_at' => now(),
            'cancelled_at' => now(),
        ]);

        Order::query()->create([
            'order_number' => 'ORD-003',
            'client_id' => $client->id,
            'status' => OrderStatus::Completed,
            'payment_status' => 'paid',
            'total_amount' => 100,
            'placed_at' => now()->subDays(2),
        ]);

        $snapshot = app(OrderMetricsService::class)->snapshot();

        $this->assertSame(2, $snapshot['today']);
        $this->assertSame(1, $snapshot['pending_fulfilment']);
        $this->assertSame(1, $snapshot['completed']);
        $this->assertSame(1, $snapshot['cancelled']);
    }
}
