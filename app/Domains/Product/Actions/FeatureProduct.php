<?php

namespace App\Domains\Product\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Product\Services\ProductService;
use App\Models\Product;

final class FeatureProduct extends BaseAction
{
    public function __construct(private readonly ProductService $products) {}

    public function handle(mixed ...$arguments): Product
    {
        /** @var Product $product */
        $product = $arguments[0];
        $featured = (bool) ($arguments[1] ?? true);

        return $this->products->feature($product, $featured);
    }
}
