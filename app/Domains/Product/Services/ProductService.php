<?php

namespace App\Domains\Product\Services;

use App\Core\Contracts\CacheStore;
use App\Core\Enums\ProductStatus;
use App\Core\Enums\VisibilityStatus;
use App\Core\Services\BaseService;
use App\Domains\Product\Data\ProductData;
use App\Domains\Product\Events\FeaturedProductChanged;
use App\Domains\Product\Events\ProductArchived;
use App\Domains\Product\Events\ProductCreated;
use App\Domains\Product\Events\ProductPublished;
use App\Domains\Product\Events\ProductUpdated;
use App\Domains\Product\Repositories\ProductCategoryRepository;
use App\Domains\Product\Repositories\ProductRepository;
use App\Domains\Product\Support\ProductCache;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Collection;

final class ProductService extends BaseService
{
    public function __construct(
        private readonly ProductRepository $products,
        private readonly ProductCategoryRepository $categories,
        private readonly CacheStore $cache,
    ) {}

    /**
     * @return Collection<int, ProductData>
     */
    public function published(?string $categorySlug = null): Collection
    {
        if ($categorySlug !== null && $categorySlug !== '') {
            $category = $this->categories->findPublishedBySlug($categorySlug);

            if ($category === null) {
                return collect();
            }

            return $this->products->inCategory($category->id)
                ->map(fn (Product $product): ProductData => $this->toData($product));
        }

        return ProductCache::rememberCollection(
            $this->cache,
            ProductCache::PUBLISHED,
            fn (): Collection => $this->products->published()
                ->map(fn (Product $product): array => $this->toData($product)->toArray()),
        )->map(fn (mixed $row): ProductData => $row instanceof ProductData
            ? $row
            : ProductData::fromArray(is_array($row) ? $row : []));
    }

    /**
     * @return Collection<int, ProductCategory>
     */
    public function categories(): Collection
    {
        return ProductCache::rememberCollection(
            $this->cache,
            ProductCache::CATEGORIES,
            fn (): Collection => $this->categories->published(),
        );
    }

    /**
     * @return Collection<int, ProductData>
     */
    public function featured(): Collection
    {
        return ProductCache::rememberCollection(
            $this->cache,
            ProductCache::FEATURED,
            fn (): Collection => $this->products->featured()
                ->map(fn (Product $product): array => $this->toData($product)->toArray()),
        )->map(fn (mixed $row): ProductData => $row instanceof ProductData
            ? $row
            : ProductData::fromArray(is_array($row) ? $row : []));
    }

    public function findPublished(string $slug): ?ProductData
    {
        $key = ProductCache::show($slug);
        $cached = $this->cache->get($key);

        if (is_array($cached)) {
            return ProductData::fromArray($cached);
        }

        $product = $this->products->findPublishedBySlug($slug);

        if ($product === null) {
            return null;
        }

        $data = $this->toData($product);
        $this->cache->put($key, $data->toArray(), now()->addHour());

        return $data;
    }

    public function modelBySlug(string $slug): ?Product
    {
        return $this->products->findPublishedBySlug($slug);
    }

    /**
     * @return Collection<int, ProductData>
     */
    public function related(Product $product): Collection
    {
        return $this->products->related($product)
            ->map(fn (Product $related): ProductData => $this->toData($related));
    }

    public function publish(Product $product): Product
    {
        $product->forceFill([
            'status' => ProductStatus::Published,
            'visibility' => $product->visibility ?? VisibilityStatus::Public,
            'published_at' => $product->published_at ?? now(),
        ])->save();

        event(new ProductPublished($product->fresh(['category'])));
        $this->forget($product->slug);

        return $product->refresh();
    }

    public function archive(Product $product): Product
    {
        $product->forceFill([
            'status' => ProductStatus::Archived,
            'is_featured' => false,
        ])->save();

        event(new ProductArchived($product->fresh(['category'])));
        $this->forget($product->slug);

        return $product->refresh();
    }

    public function feature(Product $product, bool $featured = true): Product
    {
        $product->forceFill(['is_featured' => $featured])->save();

        event(new FeaturedProductChanged($product->fresh(['category'])));
        $this->forget($product->slug);

        return $product->refresh();
    }

    public function persisted(Product $product, bool $created = false): Product
    {
        $fresh = $product->fresh(['category']) ?? $product;

        event($created ? new ProductCreated($fresh) : new ProductUpdated($fresh));
        $this->forget($fresh->slug);

        return $fresh;
    }

    public function forget(?string $slug = null): void
    {
        foreach (ProductCache::all() as $key) {
            $this->cache->forget($key);
        }

        if ($slug !== null && $slug !== '') {
            $this->cache->forget(ProductCache::show($slug));
        }
    }

    private function toData(Product $product): ProductData
    {
        $category = $product->category;
        $unitOfMeasure = $product->unit_of_measure
            ?: $product->defaultUnit?->code
            ?: $product->defaultUnit?->name;

        return ProductData::fromArray([
            ...$product->toArray(),
            'unit_of_measure' => $unitOfMeasure,
            'category_name' => $category?->name ?? '',
            'category_slug' => $category?->slug ?? '',
        ]);
    }
}
