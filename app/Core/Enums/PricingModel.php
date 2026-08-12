<?php

namespace App\Core\Enums;

enum PricingModel: string
{
    case QuoteOnRequest = 'quote-on-request';
    case StartingFrom = 'starting-from';
    case Fixed = 'fixed';
    case PerSquareMetre = 'per-square-metre';
    case Hourly = 'hourly';

    public function label(): string
    {
        return match ($this) {
            self::QuoteOnRequest => 'Quote on request',
            self::StartingFrom => 'Starting from',
            self::Fixed => 'Fixed price',
            self::PerSquareMetre => 'Per square metre',
            self::Hourly => 'Hourly',
        };
    }
}
