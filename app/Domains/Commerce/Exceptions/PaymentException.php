<?php

namespace App\Domains\Commerce\Exceptions;

use App\Core\Exceptions\DomainException;

final class PaymentException extends DomainException
{
    public static function missingTarget(): self
    {
        return new self('A payment must be recorded against either an invoice or an order.');
    }

    public static function invalidAmount(): self
    {
        return new self('Payment amount must be greater than zero.');
    }
}
