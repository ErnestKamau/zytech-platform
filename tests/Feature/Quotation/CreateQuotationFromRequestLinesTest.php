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
use App\Core\Enums\VisibilityStatus;
use App\Domains\Quotation\Services\QuotationRequestService;
use App\Domains\Quotation\Services\QuotationService;
use App\Models\Client;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CreateQuotationFromRequestLinesTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_from_request_seeds_priced_quotation_items(): void
    {
        Event::fake();

        $client = Client::query()->create([
            'type' => ClientType::Individual,
            'status' => ClientStatus::Prospect,
            'name' => 'RFQ Client',
            'email' => 'rfq@example.com',
        ]);

        $category = ProductCategory::query()->create([
            'name' => 'Fixings',
            'slug' => 'fixings',
            'is_published' => true,
            'sort_order' => 1,
        ]);

        $product = Product::query()->create([
            'product_category_id' => $category->id,
            'title' => 'E2E Bolt',
            'slug' => 'e2e-bolt',
            'sku' => 'E2E-BOLT',
            'purchase_mode' => PurchaseMode::Both,
            'status' => ProductStatus::Published,
            'visibility' => VisibilityStatus::Public,
            'pricing_model' => PricingModel::Fixed,
            'price_amount' => 125,
            'price_currency' => 'KES',
            'published_at' => now(),
            'sort_order' => 1,
        ]);

        $request = app(QuotationRequestService::class)->submit([
            'full_name' => 'RFQ Client',
            'email' => 'rfq@example.com',
            'phone' => '+254700000000',
            'project_type' => ProjectType::Residential->value,
            'county' => 'Nairobi',
            'location' => 'Westlands',
            'budget_range' => BudgetRange::Undecided->value,
            'preferred_contact_method' => PreferredContactMethod::Email->value,
            'description' => 'Need bolts for E2E test.',
        ], [], [$product->id]);

        $this->assertTrue($request->items()->exists());
        $this->assertSame($client->id, $request->client_id);

        $quotation = app(QuotationService::class)->createFromRequest($request->fresh());

        $this->assertSame(1, $quotation->items()->count());
        $this->assertSame(125.0, (float) $quotation->items()->first()->unit_price);
        $this->assertGreaterThan(0, (float) $quotation->total_amount);
    }
}
