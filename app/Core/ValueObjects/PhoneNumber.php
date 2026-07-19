<?php

namespace App\Core\ValueObjects;

use InvalidArgumentException;
use Stringable;

final readonly class PhoneNumber implements Stringable
{
    public string $value;

    public function __construct(string $value)
    {
        $normalized = preg_replace('/\s+/', '', $value) ?? '';

        if ($normalized === '' || ! preg_match('/^\+?[0-9]{7,15}$/', $normalized)) {
            throw new InvalidArgumentException("Invalid phone number [{$value}].");
        }

        $this->value = $normalized;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
