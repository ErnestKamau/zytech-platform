<?php

namespace App\Domains\Communication\Services;

use App\Core\Contracts\CacheStore;
use App\Core\Enums\AnnouncementType;
use App\Core\Services\BaseService;
use App\Domains\Communication\Events\AnnouncementPublished;
use App\Domains\Communication\Support\CommunicationCache;
use App\Models\Announcement;
use Illuminate\Support\Collection;

final class AnnouncementService extends BaseService
{
    public function __construct(private readonly CacheStore $cache) {}

    /**
     * @return Collection<int, Announcement>
     */
    public function publishedForWebsite(): Collection
    {
        return $this->published()->filter(
            fn (Announcement $announcement): bool => $announcement->show_on_website,
        )->values();
    }

    /**
     * @return Collection<int, Announcement>
     */
    public function publishedForPortal(): Collection
    {
        return $this->published()->filter(
            fn (Announcement $announcement): bool => $announcement->show_in_portal,
        )->values();
    }

    /**
     * @return Collection<int, Announcement>
     */
    public function published(): Collection
    {
        /** @var Collection<int, Announcement> $items */
        $items = $this->cache->remember(
            CommunicationCache::ANNOUNCEMENTS,
            now()->addMinutes(15),
            fn (): Collection => Announcement::query()
                ->where('is_published', true)
                ->where(function ($query): void {
                    $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })
                ->orderBy('sort_order')
                ->orderByDesc('published_at')
                ->get(),
        );

        return $items;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function publish(array $attributes): Announcement
    {
        $announcement = Announcement::query()->create([
            ...$attributes,
            'type' => $attributes['type'] ?? AnnouncementType::General,
            'is_published' => true,
            'published_at' => $attributes['published_at'] ?? now(),
        ]);

        $this->forget();
        event(new AnnouncementPublished($announcement));

        return $announcement;
    }

    public function forget(): void
    {
        $this->cache->forget(CommunicationCache::ANNOUNCEMENTS);
    }
}
