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

    public function test_funnel_includes_values_and_relationship_conversions(): void
    {
        $client = Client::query()->create([
            'type' => ClientType::Individual,
            'status' => ClientStatus::Active,
            'name' => 'Funnel Client',
            'email' => 'funnel@example.com',
        ]);

        $rfqWithQuote = QuotationRequest::query()->create([
            'reference_number' => 'RFQ-001',
            'client_id' => $client->id,
            'full_name' => 'Ada Client',
            'email' => 'ada@example.com',
            'project_type' => ProjectType::Residential->value,
            'description' => 'Need a quote',
            'status' => QuotationStatus::Pending,
            'submitted_at' => now()->subDays(2),
        ]);

        QuotationRequest::query()->create([
            'reference_number' => 'RFQ-002',
            'full_name' => 'Bob Client',
            'email' => 'bob@example.com',
            'project_type' => ProjectType::Commercial->value,
            'description' => 'Another request',
            'status' => QuotationStatus::Pending,
            'submitted_at' => now()->subDays(1),
        ]);

        $acceptedQuote = Quotation::query()->create([
            'reference_number' => 'Q-002',
            'quotation_request_id' => $rfqWithQuote->id,
            'client_id' => $client->id,
            'title' => 'Accepted quote',
            'status' => QuotationStatus::Accepted,
            'total_amount' => 2000,
            'sent_at' => now()->subDays(2),
            'accepted_at' => now()->subDay(),
        ]);

        Quotation::query()->create([
            'reference_number' => 'Q-001',
            'title' => 'Sent quote',
            'status' => QuotationStatus::Sent,
            'total_amount' => 1000,
            'sent_at' => now()->subDays(3),
        ]);

        Quotation::query()->create([
            'reference_number' => 'Q-003',
            'title' => 'Draft quote',
            'status' => QuotationStatus::Draft,
            'total_amount' => 500,
        ]);

        $salesOrder = SalesOrder::query()->create([
            'reference_number' => 'SO-FUNNEL-001',
            'client_id' => $client->id,
            'quotation_id' => $acceptedQuote->id,
            'status' => 'confirmed',
            'total_amount' => 2000,
        ]);

        Invoice::query()->create([
            'reference_number' => 'INV-FUNNEL-001',
            'client_id' => $client->id,
            'sales_order_id' => $salesOrder->id,
            'quotation_id' => $acceptedQuote->id,
            'status' => InvoiceStatus::Paid,
            'total_amount' => 2000,
            'amount_paid' => 2000,
            'amount_due' => 0,
            'issued_at' => now()->subHours(12),
        ]);

        $funnel = app(SalesAnalyticsService::class)->funnel();

        $this->assertSame(2, $funnel['stages'][0]['count']);
        $this->assertNull($funnel['stages'][0]['value']);
        $this->assertSame(2, $funnel['stages'][1]['count']);
        $this->assertSame(3000.0, $funnel['stages'][1]['value']);
        $this->assertSame(1, $funnel['stages'][2]['count']);
        $this->assertSame(2000.0, $funnel['stages'][2]['value']);
        $this->assertSame(1, $funnel['stages'][3]['count']);
        $this->assertSame(1, $funnel['stages'][4]['count']);

        // Relationship cohort conversions (last 30d) — never >100% from independent totals
        $this->assertSame(50.0, $funnel['conversions']['rfqs_to_quotes']); // 1 of 2 RFQs has quote
        $this->assertSame(50.0, $funnel['conversions']['quotes_to_accepted']); // 1 of 2 sent quotes accepted
        $this->assertSame(100.0, $funnel['conversions']['accepted_to_invoices']);
        $this->assertSame(100.0, $funnel['conversions']['invoices_to_paid']);
    }

    public function test_pipeline_value_excludes_accepted_quotes(): void
    {
        Quotation::query()->create([
            'reference_number' => 'Q-OPEN',
            'title' => 'Open',
            'status' => QuotationStatus::Sent,
            'total_amount' => 1500,
            'sent_at' => now(),
        ]);

        Quotation::query()->create([
            'reference_number' => 'Q-WON',
            'title' => 'Won',
            'status' => QuotationStatus::Accepted,
            'total_amount' => 9000,
            'sent_at' => now(),
            'accepted_at' => now(),
        ]);

        $this->assertSame(1500.0, app(SalesAnalyticsService::class)->pipelineValue());
    }

    public function test_conversion_does_not_exceed_100_when_quotes_outnumber_rfqs(): void
    {
        QuotationRequest::query()->create([
            'reference_number' => 'RFQ-ONLY',
            'full_name' => 'Solo',
            'email' => 'solo@example.com',
            'project_type' => ProjectType::Residential->value,
            'description' => 'One RFQ',
            'status' => QuotationStatus::Pending,
            'submitted_at' => now(),
        ]);

        Quotation::query()->create([
            'reference_number' => 'Q-A',
            'title' => 'A',
            'status' => QuotationStatus::Sent,
            'total_amount' => 100,
            'sent_at' => now(),
        ]);

        Quotation::query()->create([
            'reference_number' => 'Q-B',
            'title' => 'B',
            'status' => QuotationStatus::Sent,
            'total_amount' => 200,
            'sent_at' => now(),
        ]);

        $conversions = app(SalesAnalyticsService::class)->cohortConversions();

        $this->assertSame(0.0, $conversions['rfqs_to_quotes']);
        $this->assertLessThanOrEqual(100.0, $conversions['rfqs_to_quotes']);
    }
}
