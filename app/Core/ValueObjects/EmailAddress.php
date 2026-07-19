<?php

namespace App\Core\ValueObjects;

use InvalidArgumentException;
use Stringable;

final readonly class EmailAddress implements Stringable
{
    public function __construct(public string $value)
    {
        if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email address [{$value}].");
        }
    }

    public function equals(self $other): bool
    {
        return strtolower($this->value) === strtolower($other->value);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
