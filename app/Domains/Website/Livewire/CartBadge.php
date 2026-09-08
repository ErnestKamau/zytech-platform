<?php

namespace App\Domains\Website\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Commerce\Services\CartService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;

final class CartBadge extends BaseComponent
{
    #[On('cart-updated')]
    public function refreshBadge(): void
    {
        // Re-render pulls a fresh count from CartService.
    }

    public function render(CartService $carts): View
    {
        $count = 0;

        try {
            $count = (int) $carts->totals($carts->current())['item_count'];
        } catch (\Throwable) {
            $count = 0;
        }

        return view('livewire.website.cart-badge', [
            'cartCount' => $count,
        ]);
    }
}
