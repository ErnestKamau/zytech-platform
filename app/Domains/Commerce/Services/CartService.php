<?php

namespace App\Domains\Commerce\Services;

use App\Core\Enums\CartStatus;
use App\Core\Enums\ProductStatus;
use App\Core\Services\BaseService;
use App\Domains\Commerce\Exceptions\CartException;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Client;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

final class CartService extends BaseService
{
    public const SESSION_TOKEN_KEY = 'cart.session_token';

    /**
     * Resolve the active cart for the current request (client or guest session).
     */
    public function current(?Client $client = null): Cart
    {
        $client ??= $this->clientForUser(Auth::user());

        if ($client !== null) {
            $this->mergeSessionIntoClient($client);

            return $this->forClient($client);
        }

        return $this->forSession($this->sessionToken());
    }

    /**
     * Get the client's open cart, creating one if none exists yet.
     */
    public function forClient(Client $client): Cart
    {
        $cart = Cart::query()
            ->where('client_id', $client->id)
            ->active()
            ->first();

        if ($cart !== null) {
            return $cart;
        }

        return Cart::query()->create([
            'client_id' => $client->id,
            'status' => CartStatus::Active,
        ]);
    }

    public function forSession(string $sessionToken): Cart
    {
        $cart = Cart::query()
            ->where('session_token', $sessionToken)
            ->whereNull('client_id')
            ->active()
            ->first();

        if ($cart !== null) {
            return $cart;
        }

        return Cart::query()->create([
            'session_token' => $sessionToken,
            'status' => CartStatus::Active,
        ]);
    }

    /**
     * Merge a guest session cart into the client's active cart (on login).
     */
    public function mergeSessionIntoClient(Client $client): Cart
    {
        $token = session(self::SESSION_TOKEN_KEY);

        if (! is_string($token) || $token === '') {
            return $this->forClient($client);
        }

        $guest = Cart::query()
            ->where('session_token', $token)
            ->whereNull('client_id')
            ->active()
            ->with('items')
            ->first();

        $clientCart = $this->forClient($client);

        if ($guest === null || $guest->items->isEmpty()) {
            session()->forget(self::SESSION_TOKEN_KEY);

            return $clientCart;
        }

        return $this->transaction(function () use ($guest, $clientCart): Cart {
            foreach ($guest->items as $item) {
                $existingQuery = $clientCart->items()->where('product_id', $item->product_id);

                if ($item->product_variant_id !== null) {
                    $existingQuery->where('product_variant_id', $item->product_variant_id);
                } else {
                    $existingQuery->whereNull('product_variant_id');
                }

                $existing = $existingQuery->first();

                if ($existing !== null) {
                    $existing->update([
                        'quantity' => (float) $existing->quantity + (float) $item->quantity,
                        'unit_price' => $item->unit_price,
                    ]);
                    $item->forceDelete();

                    continue;
                }

                $item->update(['cart_id' => $clientCart->id]);
            }

            $guest->update([
                'status' => CartStatus::Converted,
                'converted_at' => now(),
                'session_token' => null,
            ]);

            session()->forget(self::SESSION_TOKEN_KEY);

            return $clientCart->fresh(['items']);
        });
    }

    /**
     * Add a product to the cart, or increase quantity if it's already there.
     *
     * Enforces the Domain Invariant: a quote-only product cannot enter
     * normal payment checkout.
     */
    public function addItem(Cart $cart, Product $product, float $quantity = 1): CartItem
    {
        if ($quantity <= 0) {
            throw CartException::invalidQuantity();
        }

        if (! $product->purchase_mode->allowsBuy()) {
            throw CartException::productNotBuyable($product);
        }

        if ($product->status !== ProductStatus::Published) {
            throw CartException::productUnavailable($product);
        }

        return $this->transaction(function () use ($cart, $product, $quantity): CartItem {
            $item = $cart->items()->where('product_id', $product->id)->first();

            if ($item !== null) {
                $item->update([
                    'quantity' => (float) $item->quantity + $quantity,
                    'unit_price' => $product->price_amount ?? $item->unit_price,
                ]);

                return $item->refresh();
            }

            return $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $product->price_amount ?? 0,
            ]);
        });
    }

    public function updateQuantity(CartItem $item, float $quantity): CartItem
    {
        if ($quantity <= 0) {
            throw CartException::invalidQuantity();
        }

        $item->update(['quantity' => $quantity]);

        return $item->refresh();
    }

    public function removeItem(CartItem $item): void
    {
        // Hard delete: cart lines are ephemeral and carry no audit value on
        // their own (unlike OrderItem, which is a permanent commercial
        // record). This also avoids tripping the (cart_id, product_id)
        // unique constraint if the same product is re-added later.
        $item->forceDelete();
    }

    public function clear(Cart $cart): void
    {
        $cart->items()->get()->each(fn (CartItem $item) => $item->forceDelete());
    }

    /**
     * @return array{subtotal: float, item_count: int, quantity_total: float}
     */
    public function totals(Cart $cart): array
    {
        $cart->loadMissing('items');

        return [
            'subtotal' => (float) $cart->items->sum(
                fn (CartItem $item): float => (float) $item->unit_price * (float) $item->quantity
            ),
            'item_count' => $cart->items->count(),
            'quantity_total' => (float) $cart->items->sum(
                fn (CartItem $item): float => (float) $item->quantity
            ),
        ];
    }

    public function clientForUser(?User $user): ?Client
    {
        if ($user === null) {
            return null;
        }

        return $user->clientProfile;
    }

    private function sessionToken(): string
    {
        $existing = session(self::SESSION_TOKEN_KEY);

        if (is_string($existing) && $existing !== '') {
            return $existing;
        }

        $token = (string) Str::uuid();
        session([self::SESSION_TOKEN_KEY => $token]);

        return $token;
    }
}
