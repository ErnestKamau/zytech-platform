<?php

namespace App\Core\Enums;

enum PurchaseMode: string
{
    case Buy = 'buy';
    case Quote = 'quote';
    case Both = 'both';

    public function label(): string
    {
        return match ($this) {
            self::Buy => 'Buy',
            self::Quote => 'Request quote',
            self::Both => 'Buy or quote',
        };
    }

    public function allowsQuote(): bool
    {
        return $this === self::Quote || $this === self::Both;
    }

    public function allowsBuy(): bool
    {
        return $this === self::Buy || $this === self::Both;
    }
}
