<?php

namespace Tests\Feature\Quotation;

use App\Core\Enums\BudgetRange;
use App\Core\Enums\ClientStatus;
use App\Core\Enums\ClientType;
use App\Core\Enums\PreferredContactMethod;
use App\Core\Enums\PricingModel;
use App\Core\Enums\ProductStatus;
use App\Core\Enums\ProjectType;
use App\Core\Enums\PurchaseMode;
use App\Core\Enums\QuotationStatus;
use App\Core\Enums\VisibilityStatus;
use App\Domains\Operations\Services\ActivityLogger;
use App\Domains\Quotation\Services\QuotationRequestService;
use App\Domains\Quotation\Services\QuotationService;
use App\Models\Client;
use App\Models\ClientPreference;
use App\Models\DomainActivity;
use App\Models\NotificationLog;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\QuotationRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class QuotationBusinessEventSliceTest extends TestCase
{
    use RefreshDatabase;

    public function test_submit_records_domain_activity(): void
    {
        $request = $this->submitRequest();

        $this->assertDatabaseHas('activities', [
            'subject_id' => $request->id,
            'event' => 'quotation_request.submitted',
        ]);
    }

    public function test_send_accept_and_reject_record_activity_and_notifications(): void
    {
        Mail::fake();

        $request = $this->submitRequest();
        $quotation = app(QuotationService::class)->createFromRequest($request->fresh());
        $quotation = app(QuotationService::class)->approve($quotation);
        $quotation = app(QuotationService::class)->send($quotation);

        $this->assertSame(QuotationStatus::Sent, $quotation->status);
        $this->assertTrue(
            DomainActivity::query()
                ->where('subject_id', $quotation->id)
                ->where('event', 'quotation.sent')
                ->exists()
        );

        $accepted = app(QuotationService::class)->accept($quotation->fresh());

        $this->assertSame(QuotationStatus::Accepted, $accepted->status);
        $this->assertTrue(
            DomainActivity::query()
                ->where('subject_id', $accepted->id)
                ->where('event', 'quotation.accepted')
                ->exists()
        );
        $this->assertTrue(
            NotificationLog::query()
                ->where('type', 'quotation-accepted')
                ->where('meta->quotation_id', $accepted->id)
                ->exists()
        );
    }

    public function test_reject_records_activity_and_customer_notification(): void
    {
        Mail::fake();

        $request = $this->submitRequest();
        $quotation = app(QuotationService::class)->createFromRequest($request->fresh());
        $quotation = app(QuotationService::class)->approve($quotation);
        $quotation = app(QuotationService::class)->send($quotation);

        $rejected = app(QuotationService::class)->reject($quotation->fresh(), 'Too expensive');

        $this->assertSame(QuotationStatus::Rejected, $rejected->status);
        $this->assertTrue(
            DomainActivity::query()
                ->where('subject_id', $rejected->id)
                ->where('event', 'quotation.rejected')
                ->exists()
        );
        $this->assertTrue(
            NotificationLog::query()
                ->where('type', 'quotation-rejected')
                ->where('meta->quotation_id', $rejected->id)
                ->exists()
        );
    }

    public function test_domain_activity_rolls_back_with_failed_transaction(): void
    {
        $request = $this->submitRequest();

        try {
            DB::transaction(function () use ($request): void {
                app(ActivityLogger::class)->log(
                    $request,
                    'quotation_request.force_fail',
                    ['probe' => true],
                );

                throw new \RuntimeException('force rollback');
            });
            $this->fail('Expected transaction to throw.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('force rollback', $exception->getMessage());
        }

        $this->assertFalse(
            DomainActivity::query()
                ->where('subject_id', $request->id)
                ->where('event', 'quotation_request.force_fail')
                ->exists()
        );
    }

    public function test_mail_failure_does_not_unaccept_quotation(): void
    {
        $request = $this->submitRequest();
        $quotation = app(QuotationService::class)->createFromRequest($request->fresh());
        $quotation = app(QuotationService::class)->approve($quotation);
        $quotation = app(QuotationService::class)->send($quotation);

        Mail::shouldReceive('mailer')->andReturnSelf();
        Mail::shouldReceive('to')->andReturnSelf();
        Mail::shouldReceive('send')->andThrow(new \RuntimeException('Resend unavailable'));

        $accepted = app(QuotationService::class)->accept($quotation->fresh());

        $this->assertSame(QuotationStatus::Accepted, $accepted->status);
        $this->assertTrue(
            DomainActivity::query()
                ->where('subject_id', $accepted->id)
                ->where('event', 'quotation.accepted')
                ->exists()
        );
        $this->assertTrue(
            NotificationLog::query()
                ->where('type', 'quotation-accepted')
                ->where('status', 'failed')
                ->exists()
        );
    }

    public function test_client_preference_skips_email_channel(): void
    {
        Mail::fake();

        $request = $this->submitRequest();
        ClientPreference::query()->create([
            'client_id' => $request->client_id,
            'email_notifications' => false,
            'sms_notifications' => true,
            'whatsapp_notifications' => false,
            'marketing_opt_in' => false,
        ]);

        $quotation = app(QuotationService::class)->createFromRequest($request->fresh());
        $quotation = app(QuotationService::class)->approve($quotation);
        $quotation = app(QuotationService::class)->send($quotation);

        $this->assertTrue(
            NotificationLog::query()
                ->where('type', 'quotation-sent')
                ->where('channel', 'mail')
                ->where('status', 'skipped')
                ->exists()
        );
    }

    private function submitRequest(): QuotationRequest
    {
        Client::query()->create([
            'type' => ClientType::Individual,
            'status' => ClientStatus::Prospect,
            'name' => 'RFQ Client',
            'email' => 'rfq-events@example.com',
        ]);

        $category = ProductCategory::query()->create([
            'name' => 'Events',
            'slug' => 'events-cat',
            'is_published' => true,
            'sort_order' => 1,
        ]);

        $product = Product::query()->create([
            'product_category_id' => $category->id,
            'title' => 'Event Bolt',
            'slug' => 'event-bolt',
            'sku' => 'EVT-BOLT',
            'purchase_mode' => PurchaseMode::Both,
            'status' => ProductStatus::Published,
            'visibility' => VisibilityStatus::Public,
            'pricing_model' => PricingModel::Fixed,
            'price_amount' => 100,
            'price_currency' => 'KES',
            'published_at' => now(),
            'sort_order' => 1,
        ]);

        return app(QuotationRequestService::class)->submit([
            'full_name' => 'RFQ Client',
            'email' => 'rfq-events@example.com',
            'phone' => '+254700000001',
            'project_type' => ProjectType::Residential->value,
            'county' => 'Nairobi',
            'location' => 'Westlands',
            'budget_range' => BudgetRange::Undecided->value,
            'preferred_contact_method' => PreferredContactMethod::Email->value,
            'description' => 'Need materials for event architecture test.',
        ], [], [$product->id]);
    }
}
