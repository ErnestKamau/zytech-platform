<?php

namespace App\Domains\Service\Services;

use App\Core\Contracts\CacheStore;
use App\Core\Enums\ServiceStatus;
use App\Core\Enums\VisibilityStatus;
use App\Core\Services\BaseService;
use App\Domains\Service\Data\ServiceData;
use App\Domains\Service\Events\FeaturedServiceChanged;
use App\Domains\Service\Events\ServiceArchived;
use App\Domains\Service\Events\ServiceCreated;
use App\Domains\Service\Events\ServicePublished;
use App\Domains\Service\Events\ServiceUpdated;
use App\Domains\Service\Repositories\CategoryRepository;
use App\Domains\Service\Repositories\ServiceRepository;
use App\Domains\Service\Support\ServiceCache;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Support\Collection;

final class ServiceService extends BaseService
{
    public function __construct(
        private readonly ServiceRepository $services,
        private readonly CategoryRepository $categories,
        private readonly CacheStore $cache,
    ) {}

    /**
     * @return Collection<int, ServiceData>
     */
    public function published(?string $categorySlug = null): Collection
    {
        if ($categorySlug !== null && $categorySlug !== '') {
            $category = $this->categories->findPublishedBySlug($categorySlug);

            if ($category === null) {
                return collect();
            }

            return $this->services->inCategory($category->id)
                ->map(fn (Service $service): ServiceData => $this->toData($service));
        }

        return ServiceCache::rememberCollection(
            $this->cache,
            ServiceCache::PUBLISHED,
            fn (): Collection => $this->services->published()
                ->map(fn (Service $service): array => $this->toData($service)->toArray()),
        )->map(fn (mixed $row): ServiceData => $row instanceof ServiceData
            ? $row
            : ServiceData::fromArray(is_array($row) ? $row : []));
    }

    /**
     * @return Collection<int, ServiceCategory>
     */
    public function categories(): Collection
    {
        return ServiceCache::rememberCollection(
            $this->cache,
            ServiceCache::CATEGORIES,
            fn (): Collection => $this->categories->published(),
        );
    }

    public function findPublished(string $slug): ?ServiceData
    {
        $key = ServiceCache::show($slug);
        $cached = $this->cache->get($key);

        if (is_array($cached)) {
            return ServiceData::fromArray($cached);
        }

        $service = $this->services->findPublishedBySlug($slug);

        if ($service === null) {
            return null;
        }

        $data = $this->toData($service, detailed: true);
        $this->cache->put($key, $data->toArray(), now()->addHour());

        return $data;
    }

    public function modelBySlug(string $slug): ?Service
    {
        return $this->services->findPublishedBySlug($slug);
    }

    /**
     * @return Collection<int, ServiceData>
     */
    public function related(Service $service): Collection
    {
        return $this->services->related($service)
            ->map(fn (Service $related): ServiceData => $this->toData($related));
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Service
    {
        $service = Service::query()->create($this->normalize($attributes));

        event(new ServiceCreated($service->fresh(['category'])));
        $this->forget();

        return $service->refresh();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(Service $service, array $attributes): Service
    {
        $service->fill($this->normalize($attributes))->save();

        event(new ServiceUpdated($service->fresh(['category'])));
        $this->forget($service->slug);

        return $service->refresh();
    }

    public function publish(Service $service): Service
    {
        $service->forceFill([
            'status' => ServiceStatus::Published,
            'visibility' => $service->visibility ?? VisibilityStatus::Public,
            'published_at' => $service->published_at ?? now(),
        ])->save();

        event(new ServicePublished($service->fresh(['category'])));
        $this->forget($service->slug);

        return $service->refresh();
    }

    public function archive(Service $service): Service
    {
        $service->forceFill([
            'status' => ServiceStatus::Archived,
            'is_featured' => false,
        ])->save();

        event(new ServiceArchived($service->fresh(['category'])));
        $this->forget($service->slug);

        return $service->refresh();
    }

    public function feature(Service $service, bool $featured = true): Service
    {
        $service->forceFill([
            'is_featured' => $featured,
        ])->save();

        event(new FeaturedServiceChanged($service->fresh(['category'])));
        $this->forget($service->slug);

        return $service->refresh();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function updatePricing(Service $service, array $attributes): Service
    {
        $service->fill($this->normalize($attributes))->save();

        event(new ServiceUpdated($service->fresh(['category'])));
        $this->forget($service->slug);

        return $service->refresh();
    }

    public function persisted(Service $service, bool $created = false): Service
    {
        $fresh = $service->fresh(['category']) ?? $service;

        event($created ? new ServiceCreated($fresh) : new ServiceUpdated($fresh));
        $this->forget($fresh->slug);

        return $fresh;
    }

    public function forget(?string $slug = null): void
    {
        foreach (ServiceCache::all() as $key) {
            $this->cache->forget($key);
        }

        if ($slug !== null && $slug !== '') {
            $this->cache->forget(ServiceCache::show($slug));
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    private function normalize(array $attributes): array
    {
        if (array_key_exists('gallery_keys', $attributes) && is_string($attributes['gallery_keys'])) {
            $attributes['gallery_keys'] = array_values(array_filter(array_map(
                'trim',
                preg_split('/\r\n|\r|\n|,/', $attributes['gallery_keys']) ?: [],
            )));
        }

        return $attributes;
    }

    private function toData(Service $service, bool $detailed = false): ServiceData
    {
        $category = $service->category;

        $payload = [
            ...$service->toArray(),
            'category_name' => $category?->name ?? '',
            'category_slug' => $category?->slug ?? '',
            'features' => [],
            'processes' => [],
        ];

        if ($detailed) {
            $payload['features'] = $service->features
                ->map(fn ($feature): array => [
                    'title' => $feature->title,
                    'description' => $feature->description ?? '',
                ])
                ->all();
            $payload['processes'] = $service->processes
                ->map(fn ($process): array => [
                    'title' => $process->title,
                    'description' => $process->description ?? '',
                    'sort_order' => $process->sort_order,
                ])
                ->all();
        }

        return ServiceData::fromArray($payload);
    }
}
