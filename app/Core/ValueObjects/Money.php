<?php

namespace App\Core\ValueObjects;

use InvalidArgumentException;
use Stringable;

final readonly class Money implements Stringable
{
    public function __construct(
        public string $amount,
        public string $currency = 'KES',
    ) {
        if (! is_numeric($amount)) {
            throw new InvalidArgumentException('Money amount must be numeric.');
        }

        if ($currency === '') {
            throw new InvalidArgumentException('Currency cannot be empty.');
        }
    }

    public static function from(string|float|int $amount, string $currency = 'KES'): self
    {
        return new self(number_format((float) $amount, 2, '.', ''), strtoupper($currency));
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount && $this->currency === $other->currency;
    }

    public function __toString(): string
    {
        return "{$this->currency} {$this->amount}";
    }
}
