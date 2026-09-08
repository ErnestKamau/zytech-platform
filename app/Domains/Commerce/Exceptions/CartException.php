<?php

namespace App\Domains\Commerce\Exceptions;

use App\Core\Exceptions\DomainException;
use App\Models\Product;

final class CartException extends DomainException
{
    public static function productNotBuyable(Product $product): self
    {
        return new self("\"{$product->title}\" is quote-only and cannot be added to a cart.");
    }

    public static function productUnavailable(Product $product): self
    {
        return new self("\"{$product->title}\" is not currently available for purchase.");
    }

    public static function cartIsEmpty(): self
    {
        return new self('This cart has no items to check out.');
    }

    public static function invalidQuantity(): self
    {
        return new self('Quantity must be greater than zero.');
    }
}
