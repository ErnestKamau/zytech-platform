<?php

namespace App\Domains\Website\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Product\Data\ProductData;
use App\Domains\Product\Services\ProductService;
use App\Models\ProductCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

final class ProductsPage extends BaseComponent
{
    public ?string $category = null;

    public string $search = '';

    public string $sort = 'featured';

    public function mount(?string $category = null): void
    {
        $this->category = $category;
    }

    public function updatedSearch(): void
    {
        // Livewire re-renders; no extra work.
    }

    public function render(): View
    {
        $catalogue = app(ProductService::class);
        $selected = $this->category !== null && $this->category !== ''
            ? $catalogue->categories()->first(
                fn (ProductCategory $category): bool => $category->slug === $this->category,
            )
            : null;

        if ($this->category && $selected === null) {
            abort(404);
        }

        $products = $this->filterAndSort($catalogue->published($this->category));

        return view('livewire.website.products-page', [
            'selectedCategory' => $selected,
            'categories' => $catalogue->categories(),
            'products' => $products,
            'featured' => $this->category ? collect() : $catalogue->featured(),
        ]);
    }

    /**
     * @param  Collection<int, ProductData>  $products
     * @return Collection<int, ProductData>
     */
    private function filterAndSort(Collection $products): Collection
    {
        $needle = mb_strtolower(trim($this->search));

        $filtered = $products
            ->when($needle !== '', function (Collection $collection) use ($needle): Collection {
                return $collection->filter(function (ProductData $product) use ($needle): bool {
                    $haystack = mb_strtolower(implode(' ', array_filter([
                        $product->title,
                        $product->sku,
                        $product->excerpt,
                        $product->categoryName,
                    ])));

                    return str_contains($haystack, $needle);
                });
            });

        return match ($this->sort) {
            'price_asc' => $filtered->sortBy(fn (ProductData $p): float => (float) ($p->priceAmount ?? PHP_FLOAT_MAX))->values(),
            'price_desc' => $filtered->sortByDesc(fn (ProductData $p): float => (float) ($p->priceAmount ?? 0))->values(),
            'name' => $filtered->sortBy(fn (ProductData $p): string => mb_strtolower($p->title))->values(),
            default => $filtered->sortByDesc(fn (ProductData $p): bool => $p->isFeatured)->values(),
        };
    }
}
