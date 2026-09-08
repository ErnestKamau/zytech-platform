<?php

namespace Tests\Feature\Commerce;

use App\Core\Enums\ClientStatus;
use App\Core\Enums\ClientType;
use App\Core\Enums\PricingModel;
use App\Core\Enums\ProductStatus;
use App\Core\Enums\PurchaseMode;
use App\Core\Enums\VisibilityStatus;
use App\Domains\Authentication\Events\UserLoggedIn;
use App\Domains\Commerce\Listeners\MergeCartOnLogin;
use App\Domains\Commerce\Services\CartService;
use App\Models\Client;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartMergeOnLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cart_merges_into_client_cart_on_login(): void
    {
        $user = User::factory()->create([
            'email' => 'buyer@example.com',
            'email_verified_at' => now(),
        ]);

        $client = Client::query()->create([
            'user_id' => $user->id,
            'type' => ClientType::Individual,
            'status' => ClientStatus::Active,
            'name' => 'Buyer',
            'email' => 'buyer-crm@example.com',
            'portal_access_granted_at' => now(),
        ]);

        $category = ProductCategory::query()->create([
            'name' => 'Hardware',
            'slug' => 'hardware',
            'is_published' => true,
            'sort_order' => 1,
        ]);

        $product = Product::query()->create([
            'product_category_id' => $category->id,
            'title' => 'Anchor Bolt',
            'slug' => 'anchor-bolt',
            'sku' => 'BOLT-1',
            'purchase_mode' => PurchaseMode::Buy,
            'status' => ProductStatus::Published,
            'visibility' => VisibilityStatus::Public,
            'pricing_model' => PricingModel::Fixed,
            'price_amount' => 100,
            'price_currency' => 'KES',
            'published_at' => now(),
            'sort_order' => 1,
        ]);

        $carts = app(CartService::class);
        $guestCart = $carts->current();
        $carts->addItem($guestCart, $product, 2);

        $this->assertSame(1, $guestCart->fresh()->items()->count());

        app(MergeCartOnLogin::class)->handle(new UserLoggedIn($user));

        $clientCart = $carts->forClient($client);
        $this->assertSame(1, $clientCart->items()->count());
        $this->assertSame(2.0, (float) $clientCart->items()->first()->quantity);
        $this->assertSame($product->id, $clientCart->items()->first()->product_id);
        $this->assertNull(session(CartService::SESSION_TOKEN_KEY));
    }
}
