<?php

namespace App\Domains\Operations\Services;

use App\Models\DomainActivity;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

final class ActivityLogger
{
    /**
     * Insert-only immutable activity. Never updates or deletes.
     *
     * @param  array<string, mixed>  $properties
     */
    public function log(Model $subject, string $event, array $properties = [], ?User $actor = null): DomainActivity
    {
        $activity = new DomainActivity;
        $activity->forceFill([
            'id' => (string) Str::uuid(),
            'subject_type' => $subject->getMorphClass(),
            'subject_id' => $subject->getKey(),
            'actor_id' => ($actor ?? Auth::user())?->getKey(),
            'event' => $event,
            'properties' => $properties === [] ? null : $properties,
            'created_at' => now(),
        ]);
        $activity->save();

        return $activity;
    }

    /**
     * Recent business timeline rows for dashboard / feeds.
     *
     * @return Collection<int, DomainActivity>
     */
    public function recent(int $limit = 15): Collection
    {
        return DomainActivity::query()
            ->with(['actor', 'subject'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public static function labelFor(string $event): string
    {
        return match ($event) {
            'quotation_request.submitted' => 'RFQ submitted',
            'quotation.sent' => 'Quote sent',
            'quotation.accepted' => 'Quote accepted',
            'quotation.rejected' => 'Quote rejected',
            'order.placed' => 'Order placed',
            'order.cancelled' => 'Order cancelled',
            'order.status_changed' => 'Order status changed',
            'follow_up.created' => 'Follow-up created',
            'follow_up.completed' => 'Follow-up completed',
            'follow_up.cancelled' => 'Follow-up cancelled',
            'follow_up.reassigned' => 'Follow-up reassigned',
            default => str_replace(['.', '_'], [' ', ' '], $event),
        };
    }
}
