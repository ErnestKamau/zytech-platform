<?php

namespace App\Domains\Product\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Product\Services\ProductService;
use App\Models\Product;

final class PublishProduct extends BaseAction
{
    public function __construct(private readonly ProductService $products) {}

    public function handle(mixed ...$arguments): Product
    {
        /** @var Product $product */
        $product = $arguments[0];

        return $this->products->publish($product);
    }
}
