<?php

namespace App\Core\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasPublishedState
{
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published'
            && $this->published_at !== null
            && $this->published_at <= now();
    }

    public function markAsPublished(): void
    {
        $this->forceFill([
            'status' => 'published',
            'published_at' => $this->published_at ?? now(),
        ])->save();
    }
}
