<?php

namespace App\Domains\Product\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Product\Services\ProductService;
use Illuminate\Contracts\View\View;

final class FeaturedProducts extends BaseComponent
{
    public function render(): View
    {
        return view('livewire.product.featured-products', [
            'products' => app(ProductService::class)->featured(),
        ]);
    }
}
