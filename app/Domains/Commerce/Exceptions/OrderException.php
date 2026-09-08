<?php

namespace App\Domains\Commerce\Exceptions;

use App\Core\Exceptions\DomainException;

final class OrderException extends DomainException
{
    public static function fulfillmentAlreadyStarted(): self
    {
        return new self('This order cannot be modified because fulfillment has already begun.');
    }

    public static function notCancellable(): self
    {
        return new self('This order can no longer be cancelled.');
    }
}
