<?php

namespace Tests\Feature\Commerce;

use App\Core\Enums\CartStatus;
use App\Core\Enums\ClientStatus;
use App\Core\Enums\ClientType;
use App\Core\Enums\OrderStatus;
use App\Core\Enums\PricingModel;
use App\Core\Enums\ProductStatus;
use App\Core\Enums\PurchaseMode;
use App\Core\Enums\VisibilityStatus;
use App\Domains\Commerce\Services\CartService;
use App\Domains\Commerce\Services\OrderService;
use App\Models\Client;
use App\Models\DomainActivity;
use App\Models\NotificationLog;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderBusinessEventSliceTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_and_cancel_record_activity_and_notifications(): void
    {
        Mail::fake();

        $order = $this->placeOrder();

        $this->assertSame(OrderStatus::Pending, $order->status);
        $this->assertTrue(
            DomainActivity::query()
                ->where('subject_id', $order->id)
                ->where('event', 'order.placed')
                ->exists()
        );
        $this->assertTrue(
            NotificationLog::query()
                ->where('type', 'order-placed')
                ->where('meta->order_id', $order->id)
                ->exists()
        );

        $cancelled = app(OrderService::class)->cancel($order->fresh());

        $this->assertSame(OrderStatus::Cancelled, $cancelled->status);
        $this->assertTrue(
            DomainActivity::query()
                ->where('subject_id', $cancelled->id)
                ->where('event', 'order.cancelled')
                ->exists()
        );
        $this->assertTrue(
            NotificationLog::query()
                ->where('type', 'order-cancelled')
                ->where('meta->order_id', $cancelled->id)
                ->exists()
        );
    }

    public function test_status_change_records_activity(): void
    {
        Mail::fake();

        $order = $this->placeOrder();
        // Skip confirm (inventory) — move via markProcessing after forcing confirmed status activity path
        $order->forceFill(['status' => OrderStatus::Confirmed, 'confirmed_at' => now()])->save();

        $processing = app(OrderService::class)->markProcessing($order->fresh());

        $this->assertSame(OrderStatus::Processing, $processing->status);
        $this->assertTrue(
            DomainActivity::query()
                ->where('subject_id', $processing->id)
                ->where('event', 'order.status_changed')
                ->exists()
        );
    }

    private function placeOrder(): Order
    {
        $user = User::factory()->create([
            'email' => 'order-buyer@example.com',
            'email_verified_at' => now(),
        ]);

        $client = Client::query()->create([
            'user_id' => $user->id,
            'type' => ClientType::Individual,
            'status' => ClientStatus::Active,
            'name' => 'Order Buyer',
            'email' => 'order-buyer-crm@example.com',
            'portal_access_granted_at' => now(),
        ]);

        $category = ProductCategory::query()->create([
            'name' => 'Commerce',
            'slug' => 'commerce-cat',
            'is_published' => true,
            'sort_order' => 1,
        ]);

        $product = Product::query()->create([
            'product_category_id' => $category->id,
            'title' => 'Order Widget',
            'slug' => 'order-widget',
            'sku' => 'ORD-WID',
            'purchase_mode' => PurchaseMode::Both,
            'status' => ProductStatus::Published,
            'visibility' => VisibilityStatus::Public,
            'pricing_model' => PricingModel::Fixed,
            'price_amount' => 250,
            'price_currency' => 'KES',
            'published_at' => now(),
            'sort_order' => 1,
        ]);

        $cart = app(CartService::class)->forClient($client);
        app(CartService::class)->addItem($cart, $product, 2);
        $cart->refresh();

        $this->assertSame(CartStatus::Active, $cart->status);

        return app(OrderService::class)->checkout($cart, $client, [
            'contact_name' => $client->name,
            'contact_email' => 'order-buyer@example.com',
        ]);
    }
}
