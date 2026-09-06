<?php

namespace App\Domains\Product\Events;

use App\Core\Events\BusinessEvent;
use App\Models\Product;

final class FeaturedProductChanged extends BusinessEvent
{
    public function __construct(public Product $product) {}
}
