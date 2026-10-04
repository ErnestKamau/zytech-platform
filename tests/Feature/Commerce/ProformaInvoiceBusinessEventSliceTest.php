<?php

namespace Tests\Feature\Commerce;

use App\Core\Enums\BudgetRange;
use App\Core\Enums\ClientStatus;
use App\Core\Enums\ClientType;
use App\Core\Enums\PreferredContactMethod;
use App\Core\Enums\PricingModel;
use App\Core\Enums\ProductStatus;
use App\Core\Enums\ProformaInvoiceStatus;
use App\Core\Enums\ProjectType;
use App\Core\Enums\PurchaseMode;
use App\Core\Enums\VisibilityStatus;
use App\Domains\Commerce\Actions\GenerateProformaInvoiceFromQuotation;
use App\Domains\Quotation\Services\QuotationRequestService;
use App\Domains\Quotation\Services\QuotationService;
use App\Models\Client;
use App\Models\DomainActivity;
use App\Models\NotificationLog;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProformaInvoice;
use App\Models\ProformaInvoiceDocument;
use App\Models\QuotationRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ProformaInvoiceBusinessEventSliceTest extends TestCase
{
    use RefreshDatabase;

    public function test_accepting_quotation_creates_proforma_invoice_with_correct_totals(): void
    {
        Mail::fake();

        $request = $this->submitRequest();
        $quotation = app(QuotationService::class)->createFromRequest($request->fresh());
        $quotation = app(QuotationService::class)->approve($quotation);
        $quotation = app(QuotationService::class)->send($quotation);
        $accepted = app(QuotationService::class)->accept($quotation->fresh());

        $proforma = ProformaInvoice::query()->where('quotation_id', $accepted->id)->first();

        $this->assertNotNull($proforma);
        $this->assertSame(ProformaInvoiceStatus::Issued, $proforma->status);
        $this->assertSame($accepted->client_id, $proforma->client_id);
        $this->assertEquals((float) $accepted->total_amount, (float) $proforma->total_amount);
        $this->assertEquals((float) $accepted->subtotal, (float) $proforma->subtotal);
        $this->assertTrue($proforma->items()->exists());
        $this->assertStringStartsWith('ZPI-', $proforma->reference_number);
    }

    public function test_accept_is_idempotent_for_proforma_generation(): void
    {
        Mail::fake();

        $request = $this->submitRequest();
        $quotation = app(QuotationService::class)->createFromRequest($request->fresh());
        $quotation = app(QuotationService::class)->approve($quotation);
        $quotation = app(QuotationService::class)->send($quotation);
        $accepted = app(QuotationService::class)->accept($quotation->fresh());

        app(GenerateProformaInvoiceFromQuotation::class)->handle($accepted->fresh());

        $this->assertSame(
            1,
            ProformaInvoice::query()->where('quotation_id', $accepted->id)->count(),
        );
    }

    public function test_proforma_issued_dispatches_pdf_generation_and_notification(): void
    {
        Mail::fake();

        $request = $this->submitRequest();
        $quotation = app(QuotationService::class)->createFromRequest($request->fresh());
        $quotation = app(QuotationService::class)->approve($quotation);
        $quotation = app(QuotationService::class)->send($quotation);
        $accepted = app(QuotationService::class)->accept($quotation->fresh());

        $proforma = ProformaInvoice::query()->where('quotation_id', $accepted->id)->first();

        $this->assertTrue(ProformaInvoiceDocument::query()->where('proforma_invoice_id', $proforma->id)->exists());
        $this->assertTrue(
            NotificationLog::query()
                ->where('type', 'proforma-invoice-issued')
                ->where('meta->proforma_invoice_id', $proforma->id)
                ->exists()
        );
        $this->assertTrue(
            DomainActivity::query()
                ->where('subject_id', $proforma->id)
                ->where('event', 'proforma_invoice.issued')
                ->exists()
        );
    }

    public function test_notification_failure_does_not_roll_back_proforma_creation(): void
    {
        $request = $this->submitRequest();
        $quotation = app(QuotationService::class)->createFromRequest($request->fresh());
        $quotation = app(QuotationService::class)->approve($quotation);
        $quotation = app(QuotationService::class)->send($quotation);

        Mail::shouldReceive('mailer')->andReturnSelf();
        Mail::shouldReceive('to')->andReturnSelf();
        Mail::shouldReceive('send')->andThrow(new \RuntimeException('Resend unavailable'));

        $accepted = app(QuotationService::class)->accept($quotation->fresh());

        $proforma = ProformaInvoice::query()->where('quotation_id', $accepted->id)->first();

        $this->assertNotNull($proforma);
        $this->assertSame(ProformaInvoiceStatus::Issued, $proforma->status);
        $this->assertTrue(
            NotificationLog::query()
                ->where('type', 'proforma-invoice-issued')
                ->where('status', 'failed')
                ->exists()
        );
    }

    private function submitRequest(): QuotationRequest
    {
        Client::query()->create([
            'type' => ClientType::Individual,
            'status' => ClientStatus::Prospect,
            'name' => 'Proforma Client',
            'email' => 'proforma-events@example.com',
        ]);

        $category = ProductCategory::query()->create([
            'name' => 'Events',
            'slug' => 'events-cat-proforma',
            'is_published' => true,
            'sort_order' => 1,
        ]);

        $product = Product::query()->create([
            'product_category_id' => $category->id,
            'title' => 'Event Bolt',
            'slug' => 'event-bolt-proforma',
            'sku' => 'EVT-BOLT-PI',
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
            'full_name' => 'Proforma Client',
            'email' => 'proforma-events@example.com',
            'phone' => '+254700000002',
            'project_type' => ProjectType::Residential->value,
            'county' => 'Nairobi',
            'location' => 'Westlands',
            'budget_range' => BudgetRange::Undecided->value,
            'preferred_contact_method' => PreferredContactMethod::Email->value,
            'description' => 'Need materials for proforma invoice test.',
        ], [], [$product->id]);
    }
}
