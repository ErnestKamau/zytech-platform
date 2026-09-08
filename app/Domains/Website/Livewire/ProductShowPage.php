<?php

namespace App\Domains\Website\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Commerce\Exceptions\CartException;
use App\Domains\Commerce\Services\CartService;
use App\Domains\Product\Services\ProductService;
use Illuminate\Contracts\View\View;

final class ProductShowPage extends BaseComponent
{
    public string $slug;

    public int $quantity = 1;

    public string $tab = 'overview';

    public int $activeImage = 0;

    public string $cartMessage = '';

    public bool $cartMessageError = false;

    public string $selectedVariantId = '';

    public function mount(string $slug): void
    {
        $this->slug = $slug;
    }

    public function incrementQty(): void
    {
        $this->quantity = min(999, $this->quantity + 1);
    }

    public function decrementQty(): void
    {
        $this->quantity = max(1, $this->quantity - 1);
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['overview', 'specifications', 'documents'], true)) {
            $this->tab = $tab;
        }
    }

    public function setImage(int $index): void
    {
        $this->activeImage = max(0, $index);
    }

    public function selectVariant(string $variantId): void
    {
        $this->selectedVariantId = $variantId;
    }

    public function addToCart(CartService $carts, ProductService $catalogue): void
    {
        $model = $catalogue->modelBySlug($this->slug);

        if ($model === null) {
            abort(404);
        }

        try {
            $item = $carts->addItem($carts->current(), $model, (float) $this->quantity);

            if ($this->selectedVariantId !== '') {
                $item->update(['product_variant_id' => $this->selectedVariantId]);
            }

            $this->cartMessage = 'Added to cart.';
            $this->cartMessageError = false;
            $this->dispatch('cart-updated');
        } catch (CartException $e) {
            $this->cartMessage = $e->getMessage();
            $this->cartMessageError = true;
        }
    }

    public function render(): View
    {
        $catalogue = app(ProductService::class);
        $product = $catalogue->findPublished($this->slug);
        $model = $catalogue->modelBySlug($this->slug);

        if ($product === null || $model === null) {
            abort(404);
        }

        $variants = $model->variants()->active()->orderBy('title')->get();

        return view('livewire.website.product-show-page', [
            'product' => $product,
            'related' => $catalogue->related($model),
            'variants' => $variants,
        ]);
    }
}
