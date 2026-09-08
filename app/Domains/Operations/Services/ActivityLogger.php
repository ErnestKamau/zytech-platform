<?php

namespace App\Domains\Operations\Services;

use App\Models\DomainActivity;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
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
}
