<?php

namespace App\Domains\Quotation\Services;

use App\Core\Services\BaseService;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\QuotationRequest;
use App\Models\QuotationRequestItem;

final class QuotationRequestItemService extends BaseService
{
    public function addCatalogLine(
        QuotationRequest $request,
        Product $product,
        float $quantity = 1,
        ?ProductVariant $variant = null,
        ?string $description = null,
    ): QuotationRequestItem {
        $sort = (int) $request->items()->max('sort_order') + 1;

        return $request->items()->create([
            'product_id' => $product->id,
            'product_variant_id' => $variant?->id,
            'description' => $description ?? $product->title,
            'quantity' => $quantity,
            'unit_snapshot' => $variant?->unit?->symbol
                ?? $product->defaultUnit?->symbol
                ?? $product->unit_of_measure,
            'sort_order' => $sort,
        ]);
    }

    public function addCustomLine(QuotationRequest $request, string $description, float $quantity = 1, ?string $unit = null): QuotationRequestItem
    {
        $sort = (int) $request->items()->max('sort_order') + 1;

        return $request->items()->create([
            'description' => $description,
            'quantity' => $quantity,
            'unit_snapshot' => $unit,
            'sort_order' => $sort,
        ]);
    }
}
