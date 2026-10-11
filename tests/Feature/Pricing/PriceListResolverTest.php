<?php

namespace Tests\Feature\Pricing;

use App\Core\Enums\ClientStatus;
use App\Core\Enums\ClientType;
use App\Core\Enums\PricingModel;
use App\Core\Enums\ProductStatus;
use App\Core\Enums\PurchaseMode;
use App\Core\Enums\VisibilityStatus;
use App\Domains\Pricing\Services\PriceListResolver;
use App\Models\Client;
use App\Models\PriceList;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriceListResolverTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_specific_price_wins_over_default(): void
    {
        $product = $this->product(100);
        $client = $this->client();

        $defaultList = $this->priceList('DEFAULT', isDefault: true);
        $defaultList->items()->create(['product_id' => $product->id, 'price' => 90, 'min_quantity' => 1]);

        $clientList = $this->priceList('CLIENT-A');
        $clientList->clients()->attach($client);
        $clientList->items()->create(['product_id' => $product->id, 'price' => 75, 'min_quantity' => 1]);

        $resolved = app(PriceListResolver::class)->resolve($product, $client);

        $this->assertSame(75.0, (float) $resolved->price);
    }

    public function test_falls_back_to_default_when_client_has_no_matching_list(): void
    {
        $product = $this->product(100);
        $client = $this->client();

        $defaultList = $this->priceList('DEFAULT', isDefault: true);
        $defaultList->items()->create(['product_id' => $product->id, 'price' => 90, 'min_quantity' => 1]);

        $resolved = app(PriceListResolver::class)->resolve($product, $client);

        $this->assertSame(90.0, (float) $resolved->price);
    }

    public function test_returns_null_when_nothing_matches(): void
    {
        $product = $this->product(100);

        $resolved = app(PriceListResolver::class)->resolve($product, null);

        $this->assertNull($resolved);
    }

    public function test_quantity_tier_picks_the_highest_applicable_tier(): void
    {
        $product = $this->product(100);

        $list = $this->priceList('TIERED', isDefault: true);
        $list->items()->create(['product_id' => $product->id, 'price' => 100, 'min_quantity' => 1]);
        $list->items()->create(['product_id' => $product->id, 'price' => 80, 'min_quantity' => 10]);

        $resolved = app(PriceListResolver::class)->resolve($product, null, 15.0);

        $this->assertSame(80.0, (float) $resolved->price);

        $belowTier = app(PriceListResolver::class)->resolve($product, null, 5.0);
        $this->assertSame(100.0, (float) $belowTier->price);
    }

    public function test_only_one_price_list_can_be_default(): void
    {
        $a = $this->priceList('A', isDefault: true);
        $b = $this->priceList('B');

        $this->assertTrue($a->is_default);

        $b->update(['is_default' => true]);

        $this->assertFalse($a->fresh()->is_default);
        $this->assertTrue($b->fresh()->is_default);
    }

    private function priceList(string $code, bool $isDefault = false): PriceList
    {
        return PriceList::query()->create([
            'name' => $code,
            'code' => $code.'-'.uniqid(),
            'currency' => 'KES',
            'is_default' => $isDefault,
            'active' => true,
        ]);
    }

    private function product(float $price): Product
    {
        $category = ProductCategory::query()->create([
            'name' => 'Hardware',
            'slug' => 'hardware-pricing-'.uniqid(),
            'is_published' => true,
            'sort_order' => 1,
        ]);

        return Product::query()->create([
            'product_category_id' => $category->id,
            'title' => 'Priced Widget',
            'slug' => 'priced-widget-'.uniqid(),
            'sku' => 'PW-'.uniqid(),
            'purchase_mode' => PurchaseMode::Buy,
            'status' => ProductStatus::Published,
            'visibility' => VisibilityStatus::Public,
            'pricing_model' => PricingModel::Fixed,
            'price_amount' => $price,
            'price_currency' => 'KES',
            'published_at' => now(),
            'sort_order' => 1,
        ]);
    }

    private function client(): Client
    {
        return Client::query()->create([
            'type' => ClientType::Individual,
            'status' => ClientStatus::Active,
            'name' => 'Pricing Client',
            'email' => 'pricing-'.uniqid().'@example.com',
        ]);
    }
}
