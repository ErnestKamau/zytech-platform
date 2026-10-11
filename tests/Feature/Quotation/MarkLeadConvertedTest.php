<?php

namespace Tests\Feature\Quotation;

use App\Core\Enums\BudgetRange;
use App\Core\Enums\LeadStatus;
use App\Core\Enums\PreferredContactMethod;
use App\Core\Enums\PricingModel;
use App\Core\Enums\ProductStatus;
use App\Core\Enums\ProjectType;
use App\Core\Enums\PurchaseMode;
use App\Core\Enums\VisibilityStatus;
use App\Domains\Quotation\Services\LeadService;
use App\Domains\Quotation\Services\QuotationRequestService;
use App\Domains\Quotation\Services\QuotationService;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Quotation;
use App\Models\QuotationRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MarkLeadConvertedTest extends TestCase
{
    use RefreshDatabase;

    public function test_accepting_a_quotation_converts_its_lead(): void
    {
        Mail::fake();

        $quotation = $this->acceptedQuotation();
        $lead = $quotation->salesLead;

        $this->assertNotNull($lead);
        $this->assertSame(LeadStatus::Converted, $lead->fresh()->status);
    }

    public function test_accepting_a_quotation_leaves_an_archived_lead_untouched(): void
    {
        Mail::fake();

        $quotation = $this->acceptedQuotationWithoutAccepting();
        $lead = $quotation->salesLead;

        app(LeadService::class)->archive($lead);

        app(QuotationService::class)->accept($quotation->fresh());

        $this->assertSame(LeadStatus::Archived, $lead->fresh()->status);
    }

    private function acceptedQuotationWithoutAccepting(): Quotation
    {
        $category = ProductCategory::query()->create([
            'name' => 'Events',
            'slug' => 'events-cat-lead-'.uniqid(),
            'is_published' => true,
            'sort_order' => 1,
        ]);

        $product = Product::query()->create([
            'product_category_id' => $category->id,
            'title' => 'Lead Test Product',
            'slug' => 'lead-test-product-'.uniqid(),
            'sku' => 'LEAD-'.uniqid(),
            'purchase_mode' => PurchaseMode::Both,
            'status' => ProductStatus::Published,
            'visibility' => VisibilityStatus::Public,
            'pricing_model' => PricingModel::Fixed,
            'price_amount' => 100,
            'price_currency' => 'KES',
            'published_at' => now(),
            'sort_order' => 1,
        ]);

        /** @var QuotationRequest $request */
        $request = app(QuotationRequestService::class)->submit([
            'full_name' => 'Lead Test Client',
            'email' => 'lead-test-'.uniqid().'@example.com',
            'phone' => '+254700000009',
            'project_type' => ProjectType::Residential->value,
            'county' => 'Nairobi',
            'location' => 'Westlands',
            'budget_range' => BudgetRange::Undecided->value,
            'preferred_contact_method' => PreferredContactMethod::Email->value,
            'description' => 'Need materials for lead conversion test.',
        ], [], [$product->id]);

        $quotation = app(QuotationService::class)->createFromRequest($request->fresh());
        $quotation = app(QuotationService::class)->approve($quotation);

        return app(QuotationService::class)->send($quotation)->fresh();
    }

    private function acceptedQuotation(): Quotation
    {
        $quotation = $this->acceptedQuotationWithoutAccepting();

        return app(QuotationService::class)->accept($quotation)->fresh();
    }
}
