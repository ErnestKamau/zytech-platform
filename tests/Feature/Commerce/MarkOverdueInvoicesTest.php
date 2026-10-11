<?php

namespace Tests\Feature\Commerce;

use App\Console\Commands\Invoicing\MarkOverdueInvoices;
use App\Core\Enums\ClientStatus;
use App\Core\Enums\ClientType;
use App\Core\Enums\InvoiceStatus;
use App\Core\Enums\SalesOrderStatus;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\SalesOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarkOverdueInvoicesTest extends TestCase
{
    use RefreshDatabase;

    public function test_past_due_issued_invoice_is_marked_overdue(): void
    {
        $pastDue = $this->invoice(InvoiceStatus::Issued, now()->subDays(5));
        $futureDue = $this->invoice(InvoiceStatus::Issued, now()->addDays(5));
        $paid = $this->invoice(InvoiceStatus::Paid, now()->subDays(5));
        $void = $this->invoice(InvoiceStatus::Void, now()->subDays(5));
        $partiallyPaidPastDue = $this->invoice(InvoiceStatus::PartiallyPaid, now()->subDays(5));

        $this->artisan(MarkOverdueInvoices::class)->assertExitCode(0);

        $this->assertSame(InvoiceStatus::Overdue, $pastDue->fresh()->status);
        $this->assertSame(InvoiceStatus::Overdue, $partiallyPaidPastDue->fresh()->status);
        $this->assertSame(InvoiceStatus::Issued, $futureDue->fresh()->status);
        $this->assertSame(InvoiceStatus::Paid, $paid->fresh()->status);
        $this->assertSame(InvoiceStatus::Void, $void->fresh()->status);
    }

    private function invoice(InvoiceStatus $status, $dueDate): Invoice
    {
        $client = Client::query()->create([
            'type' => ClientType::Individual,
            'status' => ClientStatus::Active,
            'name' => 'Overdue Test Client',
            'email' => 'overdue-'.uniqid().'@example.com',
        ]);

        $salesOrder = SalesOrder::query()->create([
            'reference_number' => 'ZSO-OVERDUE-'.uniqid(),
            'client_id' => $client->id,
            'status' => SalesOrderStatus::Confirmed,
            'subtotal' => 100,
            'tax_amount' => 16,
            'discount_amount' => 0,
            'total_amount' => 116,
            'currency' => 'KES',
        ]);

        return Invoice::query()->create([
            'reference_number' => 'ZINV-OVERDUE-'.uniqid(),
            'client_id' => $client->id,
            'sales_order_id' => $salesOrder->id,
            'status' => $status,
            'subtotal' => 100,
            'tax_amount' => 16,
            'discount_amount' => 0,
            'total_amount' => 116,
            'amount_paid' => 0,
            'amount_due' => 116,
            'currency' => 'KES',
            'due_date' => $dueDate,
            'issued_at' => now()->subDays(10),
        ]);
    }
}
