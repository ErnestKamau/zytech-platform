<?php

namespace Tests\Feature\Operations;

use App\Core\Enums\ClientStatus;
use App\Core\Enums\ClientType;
use App\Core\Enums\ConstructionStage;
use App\Core\Enums\InvoiceStatus;
use App\Core\Enums\ProjectStatus;
use App\Core\Enums\ProjectType;
use App\Core\Enums\QuotationStatus;
use App\Domains\Operations\Services\ActivityLogger;
use App\Domains\Operations\Services\AttentionItemsService;
use App\Domains\Project\Services\ProjectMetricsService;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\QuotationRequest;
use App\Models\SalesOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardMetricsTest extends TestCase
{
    use RefreshDatabase;

    public function test_attention_items_omit_empty_categories(): void
    {
        $this->assertSame([], app(AttentionItemsService::class)->categories());
    }

    public function test_attention_items_surface_overdue_and_rfqs(): void
    {
        $client = Client::query()->create([
            'type' => ClientType::Individual,
            'status' => ClientStatus::Active,
            'name' => 'Attention Client',
            'email' => 'attention@example.com',
        ]);

        $salesOrder = SalesOrder::query()->create([
            'reference_number' => 'SO-ATT-001',
            'client_id' => $client->id,
            'status' => 'confirmed',
            'total_amount' => 500,
        ]);

        Invoice::query()->create([
            'reference_number' => 'INV-ATT-001',
            'client_id' => $client->id,
            'sales_order_id' => $salesOrder->id,
            'status' => InvoiceStatus::Overdue,
            'total_amount' => 500,
            'amount_paid' => 0,
            'amount_due' => 500,
        ]);

        QuotationRequest::query()->create([
            'reference_number' => 'RFQ-ATT-001',
            'full_name' => 'Needs Quote',
            'email' => 'needs@example.com',
            'project_type' => ProjectType::Residential->value,
            'description' => 'Help',
            'status' => QuotationStatus::Pending,
            'submitted_at' => now()->subDays(3),
        ]);

        $categories = collect(app(AttentionItemsService::class)->categories())->keyBy('key');

        $this->assertTrue($categories->has('overdue_invoices'));
        $this->assertTrue($categories->has('rfqs_awaiting_action'));
        $this->assertTrue($categories->has('unassigned_rfqs'));
        $this->assertFalse($categories->has('orders_awaiting_fulfilment'));
    }

    public function test_project_metrics_derive_from_construction_stage(): void
    {
        $category = ProjectCategory::query()->create([
            'name' => 'Residential',
            'slug' => 'residential',
            'is_published' => true,
        ]);

        Project::query()->create([
            'project_category_id' => $category->id,
            'title' => 'Active Build',
            'slug' => 'active-build',
            'status' => ProjectStatus::Published,
            'type' => ProjectType::Residential,
            'construction_stage' => ConstructionStage::Structure,
            'progress_percent' => 40,
        ]);

        Project::query()->create([
            'project_category_id' => $category->id,
            'title' => 'Near Done',
            'slug' => 'near-done',
            'status' => ProjectStatus::Published,
            'type' => ProjectType::Commercial,
            'construction_stage' => ConstructionStage::Finishes,
            'progress_percent' => 90,
        ]);

        Project::query()->create([
            'project_category_id' => $category->id,
            'title' => 'Finished',
            'slug' => 'finished',
            'status' => ProjectStatus::Published,
            'type' => ProjectType::Residential,
            'construction_stage' => ConstructionStage::Completed,
            'progress_percent' => 100,
            'completed_on' => now()->toDateString(),
        ]);

        $snapshot = app(ProjectMetricsService::class)->snapshot();

        $this->assertSame(2, $snapshot['active']);
        $this->assertSame(1, $snapshot['near_completion']);
        $this->assertGreaterThanOrEqual(0, $snapshot['in_progress']);
    }

    public function test_activity_logger_recent_returns_latest(): void
    {
        $request = QuotationRequest::query()->create([
            'reference_number' => 'RFQ-ACT-001',
            'full_name' => 'Activity',
            'email' => 'activity@example.com',
            'project_type' => ProjectType::Residential->value,
            'description' => 'Log me',
            'status' => QuotationStatus::Pending,
            'submitted_at' => now(),
        ]);

        $logger = app(ActivityLogger::class);
        $logger->log($request, 'quotation_request.submitted');

        $recent = $logger->recent(5);

        $this->assertCount(1, $recent);
        $this->assertSame('quotation_request.submitted', $recent->first()->event);
        $this->assertSame('RFQ submitted', ActivityLogger::labelFor('quotation_request.submitted'));
    }
}
