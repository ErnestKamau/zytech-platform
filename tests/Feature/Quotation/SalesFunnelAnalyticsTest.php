<?php

namespace Tests\Feature\Quotation;

use App\Core\Enums\ClientStatus;
use App\Core\Enums\ClientType;
use App\Core\Enums\InvoiceStatus;
use App\Core\Enums\ProjectType;
use App\Core\Enums\QuotationStatus;
use App\Domains\Quotation\Services\SalesAnalyticsService;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\QuotationRequest;
use App\Models\SalesOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesFunnelAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_funnel_counts_and_conversion_percentages(): void
    {
        QuotationRequest::query()->create([
            'reference_number' => 'RFQ-001',
            'full_name' => 'Ada Client',
            'email' => 'ada@example.com',
            'project_type' => ProjectType::Residential->value,
            'description' => 'Need a quote',
            'status' => QuotationStatus::Pending,
            'submitted_at' => now(),
        ]);

        QuotationRequest::query()->create([
            'reference_number' => 'RFQ-002',
            'full_name' => 'Bob Client',
            'email' => 'bob@example.com',
            'project_type' => ProjectType::Commercial->value,
            'description' => 'Another request',
            'status' => QuotationStatus::Pending,
            'submitted_at' => now(),
        ]);

        Quotation::query()->create([
            'reference_number' => 'Q-001',
            'title' => 'Sent quote',
            'status' => QuotationStatus::Sent,
            'total_amount' => 1000,
            'sent_at' => now(),
        ]);

        Quotation::query()->create([
            'reference_number' => 'Q-002',
            'title' => 'Accepted quote',
            'status' => QuotationStatus::Accepted,
            'total_amount' => 2000,
            'sent_at' => now(),
            'accepted_at' => now(),
        ]);

        Quotation::query()->create([
            'reference_number' => 'Q-003',
            'title' => 'Draft quote',
            'status' => QuotationStatus::Draft,
            'total_amount' => 500,
        ]);

        $client = Client::query()->create([
            'type' => ClientType::Individual,
            'status' => ClientStatus::Active,
            'name' => 'Funnel Client',
            'email' => 'funnel@example.com',
        ]);

        $salesOrder = SalesOrder::query()->create([
            'reference_number' => 'SO-FUNNEL-001',
            'client_id' => $client->id,
            'status' => 'confirmed',
            'total_amount' => 2000,
        ]);

        Invoice::query()->create([
            'reference_number' => 'INV-FUNNEL-001',
            'client_id' => $client->id,
            'sales_order_id' => $salesOrder->id,
            'status' => InvoiceStatus::Paid,
            'total_amount' => 2000,
            'amount_paid' => 2000,
            'amount_due' => 0,
        ]);

        $funnel = app(SalesAnalyticsService::class)->funnel();

        $this->assertSame(2, $funnel['stages'][0]['count']);
        $this->assertSame(2, $funnel['stages'][1]['count']);
        $this->assertSame(1, $funnel['stages'][2]['count']);
        $this->assertSame(1, $funnel['stages'][3]['count']);
        $this->assertSame(1, $funnel['stages'][4]['count']);

        $this->assertSame(100.0, $funnel['conversions']['rfqs_to_quotes']);
        $this->assertSame(50.0, $funnel['conversions']['quotes_to_accepted']);
        $this->assertSame(100.0, $funnel['conversions']['accepted_to_invoices']);
        $this->assertSame(100.0, $funnel['conversions']['invoices_to_paid']);
    }
}
