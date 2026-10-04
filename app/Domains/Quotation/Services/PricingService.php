<?php

namespace App\Domains\Quotation\Services;

use App\Core\Services\BaseService;
use App\Models\Quotation;

final class PricingService extends BaseService
{
    public const DEFAULT_TAX_RATE = 16.0;

    public function lineTotal(float $quantity, float $unitPrice): float
    {
        return round($quantity * $unitPrice, 2);
    }

    /**
     * Discount is applied before tax.
     *
     * @param  iterable<array{quantity?: mixed, unit_price?: mixed, is_optional?: mixed}>  $lines
     * @return array{subtotal: float, tax_amount: float, discount_amount: float, total_amount: float}
     */
    public function summarize(iterable $lines, float $taxRate, float $discount): array
    {
        $subtotal = 0.0;

        foreach ($lines as $line) {
            if (filter_var($line['is_optional'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
                continue;
            }

            $subtotal += $this->lineTotal((float) ($line['quantity'] ?? 0), (float) ($line['unit_price'] ?? 0));
        }

        $subtotal = round($subtotal, 2);
        $discount = min(max(0, round($discount, 2)), $subtotal);
        $taxAmount = round(($subtotal - $discount) * max(0, $taxRate) / 100, 2);

        return [
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discount,
            'total_amount' => round($subtotal - $discount + $taxAmount, 2),
        ];
    }

    /**
     * @return array{subtotal: float, tax_amount: float, discount_amount: float, total_amount: float}
     */
    public function totals(Quotation $quotation): array
    {
        $quotation->loadMissing('items');

        return $this->summarize(
            $quotation->items->map(fn ($item): array => $item->only(['quantity', 'unit_price', 'is_optional'])),
            (float) ($quotation->tax_rate ?? self::DEFAULT_TAX_RATE),
            (float) $quotation->discount_amount,
        );
    }
}
