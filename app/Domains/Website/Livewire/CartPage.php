<?php

namespace App\Domains\Website\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Commerce\Exceptions\CartException;
use App\Domains\Commerce\Services\CartService;
use App\Models\CartItem;
use Illuminate\Contracts\View\View;

final class CartPage extends BaseComponent
{
    public string $flash = '';

    public function updateQuantity(string $itemId, float $quantity, CartService $carts): void
    {
        $item = $this->ownedItem($itemId, $carts);

        try {
            if ($quantity <= 0) {
                $carts->removeItem($item);
                $this->flash = 'Item removed.';
                $this->dispatch('cart-updated');

                return;
            }

            $carts->updateQuantity($item, $quantity);
            $this->flash = 'Quantity updated.';
            $this->dispatch('cart-updated');
        } catch (CartException $e) {
            $this->flash = $e->getMessage();
        }
    }

    public function removeItem(string $itemId, CartService $carts): void
    {
        $item = $this->ownedItem($itemId, $carts);
        $carts->removeItem($item);
        $this->flash = 'Item removed.';
        $this->dispatch('cart-updated');
    }

    public function clear(CartService $carts): void
    {
        $carts->clear($carts->current());
        $this->flash = 'Cart cleared.';
        $this->dispatch('cart-updated');
    }

    public function render(CartService $carts): View
    {
        $cart = $carts->current()->load(['items.product', 'items.variant']);
        $totals = $carts->totals($cart);

        return view('livewire.website.cart-page', [
            'cart' => $cart,
            'totals' => $totals,
            'canCheckout' => $cart->items->isNotEmpty() && $carts->clientForUser(auth()->user()) !== null,
        ]);
    }

    private function ownedItem(string $itemId, CartService $carts): CartItem
    {
        $cart = $carts->current();
        $item = $cart->items()->whereKey($itemId)->first();
        abort_unless($item !== null, 404);

        return $item;
    }
}
