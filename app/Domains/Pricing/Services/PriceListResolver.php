<?php

namespace App\Domains\Pricing\Services;

use App\Models\Client;
use App\Models\PriceList;
use App\Models\PriceListItem;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

final class PriceListResolver
{
    /**
     * Resolve the best-matching PriceListItem for a product, preferring a
     * client-specific pricelist over the system default. Returns null when
     * nothing matches, so callers can fall back to Product.price_amount.
     */
    public function resolve(Product $product, ?Client $client, float $quantity = 1.0): ?PriceListItem
    {
        if ($client !== null) {
            $clientItem = $this->bestItemFromLists(
                $client->priceLists()->where('active', true)->get(),
                $product,
                $quantity,
            );

            if ($clientItem !== null) {
                return $clientItem;
            }
        }

        $defaultList = PriceList::query()->where('is_default', true)->where('active', true)->first();

        if ($defaultList === null) {
            return null;
        }

        return $this->bestItemFromLists(collect([$defaultList]), $product, $quantity);
    }

    /**
     * @param  Collection<int, PriceList>  $priceLists
     */
    private function bestItemFromLists($priceLists, Product $product, float $quantity): ?PriceListItem
    {
        $today = Carbon::today();

        $validLists = $priceLists->filter(function (PriceList $list) use ($today): bool {
            if ($list->valid_from !== null && $today->lt($list->valid_from)) {
                return false;
            }

            return ! ($list->valid_until !== null && $today->gt($list->valid_until));
        });

        if ($validLists->isEmpty()) {
            return null;
        }

        return PriceListItem::query()
            ->whereIn('price_list_id', $validLists->pluck('id'))
            ->where('product_id', $product->id)
            ->where('min_quantity', '<=', $quantity)
            ->orderByDesc('min_quantity')
            ->first();
    }
}
