<?php

namespace App\Support\Helpers;

use App\Core\ValueObjects\Money;

final class MoneyFormatter
{
    public static function format(float|int|string $amount, string $currency = 'KES'): string
    {
        return (string) Money::from($amount, $currency);
    }

    /**
     * Compact display for dense dashboard cards (e.g. KES 2.45M).
     */
    public static function compact(float|int|string $amount, string $currency = 'KES'): string
    {
        $value = (float) $amount;
        $abs = abs($value);
        $sign = $value < 0 ? '-' : '';

        if ($abs >= 1_000_000) {
            return sprintf('%s%s %sM', $sign, strtoupper($currency), rtrim(rtrim(number_format($abs / 1_000_000, 2, '.', ''), '0'), '.'));
        }

        if ($abs >= 1_000) {
            return sprintf('%s%s %sK', $sign, strtoupper($currency), rtrim(rtrim(number_format($abs / 1_000, 1, '.', ''), '0'), '.'));
        }

        return self::format($value, $currency);
    }
}
