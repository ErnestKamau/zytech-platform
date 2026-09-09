<?php

namespace Tests\Feature\Portal;

use App\Core\Enums\ClientStatus;
use App\Core\Enums\ClientType;
use App\Core\Enums\ConstructionStage;
use App\Core\Enums\InvoiceStatus;
use App\Core\Enums\OrderStatus;
use App\Core\Enums\PaymentStatus;
use App\Core\Enums\ProjectStatus;
use App\Core\Enums\ProjectType;
use App\Core\Enums\QuotationStatus;
use App\Core\Enums\TicketStatus;
use App\Domains\Portal\Services\ClientActionItemsService;
use App\Domains\Portal\Services\DashboardService;
use App\Models\Client;
use App\Models\ClientTimeline;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Quotation;
use App\Models\SalesOrder;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_empty_client_dashboard_is_intentional(): void
    {
        $client = $this->makePortalClient('Empty Client', 'empty@example.com');

        $data = app(DashboardService::class)->forClient($client);

        $this->assertSame(0, $data->pendingQuotations);
        $this->assertSame(0, $data->activeProjects);
        $this->assertSame(0.0, $data->outstanding);
        $this->assertSame([], $data->actionItems);
        $this->assertSame([], $data->quotations);
        $this->assertSame([], $data->orders);
    }

    public function test_sent_quotation_counts_as_pending_and_action_item(): void
    {
        $client = $this->makePortalClient('Quote Client', 'quote@example.com');

        Quotation::query()->create([
            'reference_number' => 'QT-DASH-001',
            'client_id' => $client->id,
            'title' => 'Kitchen renovation',
            'status' => QuotationStatus::Sent,
            'total_amount' => 250000,
            'currency' => 'KES',
            'sent_at' => now(),
        ]);

        Quotation::query()->create([
            'reference_number' => 'QT-DASH-PREP',
            'client_id' => $client->id,
            'title' => 'Internal prep',
            'status' => QuotationStatus::Preparing,
            'total_amount' => 10000,
            'currency' => 'KES',
        ]);

        $data = app(DashboardService::class)->forClient($client);

        $this->assertSame(1, $data->pendingQuotations);
        $this->assertCount(1, $data->actionItems);
        $this->assertStringContainsString('QT-DASH-001', $data->actionItems[0]['title']);
        $this->assertTrue(collect($data->quotations)->contains(fn (array $q): bool => $q['reviewable'] === true));
    }

    public function test_accepted_quotation_is_not_an_action_item(): void
    {
        $client = $this->makePortalClient('Accepted Client', 'accepted@example.com');

        Quotation::query()->create([
            'reference_number' => 'QT-ACCEPTED',
            'client_id' => $client->id,
            'title' => 'Accepted quote',
            'status' => QuotationStatus::Accepted,
            'total_amount' => 180000,
            'currency' => 'KES',
            'sent_at' => now()->subDay(),
            'accepted_at' => now(),
        ]);

        $items = app(ClientActionItemsService::class)->forClient($client);

        $this->assertSame([], $items);
        $this->assertSame(0, app(DashboardService::class)->forClient($client)->pendingQuotations);
    }

    public function test_overdue_invoice_surfaces_in_action_required_and_finance(): void
    {
        $client = $this->makePortalClient('Invoice Client', 'invoice@example.com');
        $salesOrder = SalesOrder::query()->create([
            'reference_number' => 'SO-DASH-001',
            'client_id' => $client->id,
            'status' => 'confirmed',
            'total_amount' => 35000,
        ]);

        Invoice::query()->create([
            'reference_number' => 'INV-DASH-001',
            'client_id' => $client->id,
            'sales_order_id' => $salesOrder->id,
            'status' => InvoiceStatus::Overdue,
            'total_amount' => 35000,
            'amount_paid' => 0,
            'amount_due' => 35000,
            'currency' => 'KES',
            'due_date' => now()->subDays(4)->toDateString(),
        ]);

        $data = app(DashboardService::class)->forClient($client);

        $this->assertSame(35000.0, $data->outstanding);
        $this->assertSame(1, $data->finance['overdue']);
        $this->assertTrue(collect($data->actionItems)->contains(
            fn (array $item): bool => str_contains($item['title'], 'INV-DASH-001')
        ));
    }

    public function test_outstanding_uses_amount_due_after_partial_payment(): void
    {
        $client = $this->makePortalClient('Partial Client', 'partial@example.com');
        $salesOrder = SalesOrder::query()->create([
            'reference_number' => 'SO-PARTIAL',
            'client_id' => $client->id,
            'status' => 'confirmed',
            'total_amount' => 100000,
        ]);

        $invoice = Invoice::query()->create([
            'reference_number' => 'INV-PARTIAL',
            'client_id' => $client->id,
            'sales_order_id' => $salesOrder->id,
            'status' => InvoiceStatus::PartiallyPaid,
            'total_amount' => 100000,
            'amount_paid' => 40000,
            'amount_due' => 60000,
            'currency' => 'KES',
        ]);

        Payment::query()->create([
            'invoice_id' => $invoice->id,
            'amount' => 40000,
            'status' => PaymentStatus::Completed,
            'paid_at' => now(),
        ]);

        $data = app(DashboardService::class)->forClient($client);

        $this->assertSame(60000.0, $data->outstanding);
        $this->assertSame(40000.0, $data->finance['paid_this_year']);
    }

    public function test_cancelled_orders_are_excluded_from_orders_panel(): void
    {
        $client = $this->makePortalClient('Order Client', 'order@example.com');

        Order::query()->create([
            'order_number' => 'ORD-ACTIVE',
            'client_id' => $client->id,
            'status' => OrderStatus::Processing,
            'payment_status' => 'unpaid',
            'total_amount' => 45000,
            'placed_at' => now(),
        ]);

        Order::query()->create([
            'order_number' => 'ORD-CANCELLED',
            'client_id' => $client->id,
            'status' => OrderStatus::Cancelled,
            'payment_status' => 'unpaid',
            'total_amount' => 12000,
            'placed_at' => now(),
            'cancelled_at' => now(),
        ]);

        $data = app(DashboardService::class)->forClient($client);

        $this->assertCount(1, $data->orders);
        $this->assertSame('ORD-ACTIVE', $data->orders[0]['order_number']);
    }

    public function test_client_cannot_see_another_clients_dashboard_data(): void
    {
        $clientA = $this->makePortalClient('Client A', 'a@example.com');
        $clientB = $this->makePortalClient('Client B', 'b@example.com');

        Quotation::query()->create([
            'reference_number' => 'QT-B-ONLY',
            'client_id' => $clientB->id,
            'title' => 'Secret quote',
            'status' => QuotationStatus::Sent,
            'total_amount' => 999999,
            'currency' => 'KES',
            'sent_at' => now(),
        ]);

        $data = app(DashboardService::class)->forClient($clientA);

        $this->assertSame(0, $data->pendingQuotations);
        $this->assertSame([], $data->actionItems);
        $this->assertFalse(collect($data->quotations)->contains(
            fn (array $q): bool => $q['reference_number'] === 'QT-B-ONLY'
        ));
    }

    public function test_waiting_ticket_appears_in_action_required(): void
    {
        $client = $this->makePortalClient('Ticket Client', 'ticket@example.com');

        SupportTicket::query()->create([
            'reference_number' => 'TCK-001',
            'client_id' => $client->id,
            'subject' => 'Need clarification',
            'body' => 'Please confirm the site visit window.',
            'status' => TicketStatus::Waiting,
        ]);

        $items = app(ClientActionItemsService::class)->forClient($client);

        $this->assertCount(1, $items);
        $this->assertSame('info', $items[0]['severity']);
        $this->assertStringContainsString('TCK-001', $items[0]['title']);
    }

    public function test_activity_uses_client_timeline(): void
    {
        $client = $this->makePortalClient('Timeline Client', 'timeline@example.com');

        ClientTimeline::query()->create([
            'client_id' => $client->id,
            'event_type' => 'quotation-sent',
            'title' => 'Quotation QT-0018 was sent',
            'description' => 'Zytech sent your quotation',
            'occurred_at' => now()->subHours(2),
        ]);

        $data = app(DashboardService::class)->forClient($client);

        $this->assertCount(1, $data->activity);
        $this->assertSame('Quotation QT-0018 was sent', $data->activity[0]['title']);
    }

    public function test_active_projects_use_progress_percent(): void
    {
        $client = $this->makePortalClient('Project Client', 'project@example.com');
        $category = ProjectCategory::query()->create([
            'name' => 'Residential',
            'slug' => 'residential-dash',
            'is_published' => true,
        ]);

        $project = Project::query()->create([
            'project_category_id' => $category->id,
            'title' => 'Karen Residence',
            'slug' => 'karen-residence-dash',
            'status' => ProjectStatus::Published,
            'type' => ProjectType::Residential,
            'construction_stage' => ConstructionStage::Structure,
            'progress_percent' => 78,
        ]);

        $client->projects()->attach($project->id);

        $data = app(DashboardService::class)->forClient($client);

        $this->assertSame(1, $data->activeProjects);
        $this->assertSame(78, $data->projects[0]['progress_percent']);
        $this->assertSame('Karen Residence', $data->projects[0]['title']);
    }

    private function makePortalClient(string $name, string $email): Client
    {
        $user = User::factory()->create([
            'name' => $name,
            'email' => $email,
            'email_verified_at' => now(),
        ]);

        return Client::query()->create([
            'user_id' => $user->id,
            'type' => ClientType::Individual,
            'status' => ClientStatus::Active,
            'name' => $name,
            'email' => $email,
            'portal_access_granted_at' => now(),
        ]);
    }
}
