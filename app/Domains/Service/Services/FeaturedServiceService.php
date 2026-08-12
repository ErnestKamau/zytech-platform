<?php

namespace App\Domains\Service\Services;

use App\Core\Contracts\CacheStore;
use App\Core\Services\BaseService;
use App\Domains\Service\Data\ServiceData;
use App\Domains\Service\Repositories\ServiceRepository;
use App\Domains\Service\Support\ServiceCache;
use App\Models\Service;
use Illuminate\Support\Collection;

final class FeaturedServiceService extends BaseService
{
    public function __construct(
        private readonly ServiceRepository $services,
        private readonly CacheStore $cache,
    ) {}

    /**
     * @return Collection<int, ServiceData>
     */
    public function current(): Collection
    {
        return ServiceCache::rememberCollection(
            $this->cache,
            ServiceCache::FEATURED,
            fn (): Collection => $this->services->featured()
                ->map(fn (Service $service): array => $this->toArray($service)),
        )->map(fn (mixed $row): ServiceData => $row instanceof ServiceData
            ? $row
            : ServiceData::fromArray(is_array($row) ? $row : []));
    }

    /**
     * @return array<string, mixed>
     */
    private function toArray(Service $service): array
    {
        $category = $service->category;

        return ServiceData::fromArray([
            ...$service->toArray(),
            'category_name' => $category?->name ?? '',
            'category_slug' => $category?->slug ?? '',
        ])->toArray();
    }
}
