<?php

namespace App\Domains\Quotation\Services;

use App\Core\Services\BaseService;
use App\Models\Quotation;

final class PricingService extends BaseService
{
    public function lineTotal(float $quantity, float $unitPrice): float
    {
        return round($quantity * $unitPrice, 2);
    }

    /**
     * @return array{subtotal: float, tax_amount: float, discount_amount: float, total_amount: float}
     */
    public function totals(Quotation $quotation): array
    {
        $quotation->loadMissing('items');

        $subtotal = (float) $quotation->items
            ->where('is_optional', false)
            ->sum('line_total');

        $taxAmount = round($subtotal * 0.16, 2);
        $discount = (float) $quotation->discount_amount;
        $total = max(0, round($subtotal + $taxAmount - $discount, 2));

        return [
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discount,
            'total_amount' => $total,
        ];
    }
}
