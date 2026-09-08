<?php

namespace App\Domains\Commerce\Exceptions;

use App\Core\Exceptions\DomainException;

final class FulfillmentException extends DomainException
{
    public static function terminal(): self
    {
        return new self('This fulfillment is already completed or cancelled.');
    }

    public static function alreadyDelivered(): self
    {
        return new self('Delivered fulfillments cannot be cancelled.');
    }
}
