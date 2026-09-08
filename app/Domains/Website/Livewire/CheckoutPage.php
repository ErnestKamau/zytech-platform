<?php

namespace App\Domains\Website\Livewire;

use App\Core\Enums\FulfillmentMethod;
use App\Core\Livewire\BaseComponent;
use App\Domains\Commerce\Exceptions\CartException;
use App\Domains\Commerce\Exceptions\OrderException;
use App\Domains\Commerce\Services\CartService;
use App\Domains\Commerce\Services\OrderService;
use Illuminate\Contracts\View\View;

final class CheckoutPage extends BaseComponent
{
    public string $fulfillment_method = 'delivery';

    public string $contact_name = '';

    public string $contact_email = '';

    public string $contact_phone = '';

    public string $billing_address = '';

    public string $delivery_address = '';

    public string $notes = '';

    public string $error = '';

    public function mount(CartService $carts): void
    {
        $client = $carts->clientForUser(auth()->user());

        if ($client === null) {
            $this->redirect(route('login'));

            return;
        }

        $cart = $carts->current($client);

        if ($cart->isEmpty()) {
            $this->redirect(route('cart'));

            return;
        }

        $this->contact_name = (string) ($client->name ?? '');
        $this->contact_email = (string) ($client->email ?? '');
        $this->contact_phone = (string) ($client->phone ?? '');
    }

    public function placeOrder(CartService $carts, OrderService $orders): void
    {
        $this->error = '';
        $client = $carts->clientForUser(auth()->user());
        abort_unless($client !== null, 403);

        $this->validate([
            'fulfillment_method' => 'required|in:delivery,pickup',
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'billing_address' => 'nullable|string|max:2000',
            'delivery_address' => 'nullable|string|max:2000',
            'notes' => 'nullable|string|max:2000',
        ]);

        try {
            $order = $orders->checkout($carts->current($client), $client, [
                'fulfillment_method' => FulfillmentMethod::from($this->fulfillment_method),
                'contact_name' => $this->contact_name,
                'contact_email' => $this->contact_email,
                'contact_phone' => $this->contact_phone ?: null,
                'billing_address' => $this->billing_address ?: null,
                'delivery_address' => $this->delivery_address ?: null,
                'notes' => $this->notes ?: null,
            ]);

            $this->redirect(route('checkout.success', $order->order_number));
        } catch (CartException|OrderException $e) {
            $this->error = $e->getMessage();
        }
    }

    public function render(CartService $carts): View
    {
        $client = $carts->clientForUser(auth()->user());
        abort_unless($client !== null, 403);

        $cart = $carts->current($client)->load(['items.product']);
        $totals = $carts->totals($cart);

        return view('livewire.website.checkout-page', [
            'cart' => $cart,
            'totals' => $totals,
        ]);
    }
}
