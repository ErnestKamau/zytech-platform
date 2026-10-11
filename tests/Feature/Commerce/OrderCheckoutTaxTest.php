<?php

namespace Tests\Feature\Commerce;

use App\Core\Enums\ClientStatus;
use App\Core\Enums\ClientType;
use App\Core\Enums\PricingModel;
use App\Core\Enums\ProductStatus;
use App\Core\Enums\PurchaseMode;
use App\Core\Enums\VisibilityStatus;
use App\Domains\Commerce\Services\CartService;
use App\Domains\Commerce\Services\OrderService;
use App\Models\Client;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderCheckoutTaxTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_applies_default_tax_rate_only_to_taxable_products(): void
    {
        $client = Client::query()->create([
            'type' => ClientType::Individual,
            'status' => ClientStatus::Active,
            'name' => 'Checkout Tax Client',
            'email' => 'checkout-tax-'.uniqid().'@example.com',
        ]);

        $category = ProductCategory::query()->create([
            'name' => 'Hardware',
            'slug' => 'hardware-tax-'.uniqid(),
            'is_published' => true,
            'sort_order' => 1,
        ]);

        $taxable = Product::query()->create([
            'product_category_id' => $category->id,
            'title' => 'Taxable Widget',
            'slug' => 'taxable-widget-'.uniqid(),
            'sku' => 'TAX-1',
            'purchase_mode' => PurchaseMode::Buy,
            'status' => ProductStatus::Published,
            'visibility' => VisibilityStatus::Public,
            'pricing_model' => PricingModel::Fixed,
            'price_amount' => 100,
            'price_currency' => 'KES',
            'taxable' => true,
            'published_at' => now(),
            'sort_order' => 1,
        ]);

        $exempt = Product::query()->create([
            'product_category_id' => $category->id,
            'title' => 'Exempt Widget',
            'slug' => 'exempt-widget-'.uniqid(),
            'sku' => 'TAX-2',
            'purchase_mode' => PurchaseMode::Buy,
            'status' => ProductStatus::Published,
            'visibility' => VisibilityStatus::Public,
            'pricing_model' => PricingModel::Fixed,
            'price_amount' => 50,
            'price_currency' => 'KES',
            'taxable' => false,
            'published_at' => now(),
            'sort_order' => 2,
        ]);

        $carts = app(CartService::class);
        $cart = $carts->forClient($client);
        $carts->addItem($cart, $taxable, 2);
        $carts->addItem($cart, $exempt, 1);

        $order = app(OrderService::class)->checkout($cart->fresh(), $client);

        // Taxable line: 2 * 100 * 16% = 32. Exempt line: 50 * 0% = 0.
        $this->assertSame(32.0, (float) $order->tax_amount);
        $this->assertSame(250.0, (float) $order->subtotal);
        $this->assertSame(282.0, (float) $order->total_amount);

        $taxableItem = $order->items()->where('product_id', $taxable->id)->firstOrFail();
        $exemptItem = $order->items()->where('product_id', $exempt->id)->firstOrFail();

        $this->assertSame(32.0, (float) $taxableItem->tax);
        $this->assertSame(0.0, (float) $exemptItem->tax);
    }
}
