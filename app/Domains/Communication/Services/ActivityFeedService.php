<?php

namespace App\Domains\Communication\Services;

use App\Core\Services\BaseService;
use App\Models\ActivityFeedItem;
use App\Models\User;
use Illuminate\Support\Collection;

final class ActivityFeedService extends BaseService
{
    /**
     * @param  array<string, mixed>|null  $meta
     */
    public function record(
        ?User $user,
        string $eventType,
        string $title,
        ?string $description = null,
        ?array $meta = null,
    ): ActivityFeedItem {
        return ActivityFeedItem::query()->create([
            'user_id' => $user?->id,
            'event_type' => $eventType,
            'title' => $title,
            'description' => $description,
            'meta' => $meta,
            'occurred_at' => now(),
        ]);
    }

    /**
     * @return Collection<int, ActivityFeedItem>
     */
    public function recent(?User $user = null, int $limit = 20): Collection
    {
        $query = ActivityFeedItem::query()->orderByDesc('occurred_at')->limit($limit);

        if ($user !== null) {
            $query->where(function ($builder) use ($user): void {
                $builder->whereNull('user_id')->orWhere('user_id', $user->id);
            });
        }

        return $query->get();
    }
}
