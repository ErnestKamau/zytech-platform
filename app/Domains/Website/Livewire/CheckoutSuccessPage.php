<?php

namespace App\Domains\Website\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Models\Order;
use Illuminate\Contracts\View\View;

final class CheckoutSuccessPage extends BaseComponent
{
    public string $orderNumber;

    public function mount(string $orderNumber): void
    {
        $this->orderNumber = $orderNumber;
    }

    public function render(): View
    {
        $order = Order::query()
            ->where('order_number', $this->orderNumber)
            ->with('items')
            ->firstOrFail();

        if (auth()->check() && $order->client_id) {
            abort_unless(auth()->user()?->clientProfile?->id === $order->client_id, 403);
        }

        return view('livewire.website.checkout-success-page', [
            'order' => $order,
        ]);
    }
}
