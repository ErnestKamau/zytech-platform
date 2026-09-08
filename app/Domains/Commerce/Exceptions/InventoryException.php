<?php

namespace App\Domains\Commerce\Exceptions;

use App\Core\Exceptions\DomainException;
use App\Models\Product;

final class InventoryException extends DomainException
{
    public static function insufficientStock(Product $product, float $requested, float $available): self
    {
        return new self(sprintf(
            'Cannot reserve %s x "%s": only %s available in stock.',
            $requested,
            $product->title,
            $available,
        ));
    }

    public static function invalidQuantity(): self
    {
        return new self('Inventory quantity must be greater than zero.');
    }

    public static function reservationNotActive(): self
    {
        return new self('This stock reservation is no longer active.');
    }
}
