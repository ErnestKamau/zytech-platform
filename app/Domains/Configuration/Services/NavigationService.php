<?php

namespace App\Domains\Configuration\Services;

use App\Core\Contracts\CacheStore;
use App\Core\Enums\NavigationLocation;
use App\Core\Services\BaseService;
use App\Domains\Configuration\Data\NavigationData;
use App\Domains\Configuration\Events\NavigationUpdated;
use App\Domains\Configuration\Repositories\NavigationRepository;
use App\Domains\Configuration\Support\ConfigurationCache;
use App\Models\NavigationItem;
use App\Models\NavigationMenu;

final class NavigationService extends BaseService
{
    public function __construct(
        private readonly NavigationRepository $menus,
        private readonly CacheStore $cache,
    ) {}

    public function published(NavigationLocation $location): ?NavigationData
    {
        return $this->cache->remember(
            ConfigurationCache::navigation($location->value),
            now()->addHour(),
            function () use ($location): ?NavigationData {
                $menu = $this->menus->publishedFor($location);

                if ($menu === null) {
                    return null;
                }

                return $this->toData($menu);
            },
        );
    }

    public function publish(NavigationMenu $menu): NavigationMenu
    {
        return $this->transaction(function () use ($menu): NavigationMenu {
            NavigationMenu::query()
                ->where('location', $menu->location)
                ->where('id', '!=', $menu->id)
                ->update(['is_published' => false]);

            $menu->forceFill(['is_published' => true])->save();

            $this->forgetLocation($menu->location);
            event(new NavigationUpdated($menu->fresh(['items'])));

            return $menu->refresh();
        });
    }

    public function forgetLocation(NavigationLocation $location): void
    {
        $this->cache->forget(ConfigurationCache::navigation($location->value));
    }

    public function toData(NavigationMenu $menu): NavigationData
    {
        $items = $menu->relationLoaded('visibleItems')
            ? $menu->visibleItems
            : $menu->visibleItems()->get();

        return new NavigationData(
            name: $menu->name,
            location: $menu->location,
            isPublished: $menu->is_published,
            items: $items->map(fn (NavigationItem $item): array => [
                'label' => $item->label,
                'href' => $item->href(),
                'target' => $item->target,
                'route_name' => $item->route_name,
            ])->values()->all(),
        );
    }
}
