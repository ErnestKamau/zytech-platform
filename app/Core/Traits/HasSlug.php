<?php

namespace App\Core\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    public static function bootHasSlug(): void
    {
        static::creating(function ($model): void {
            if (empty($model->slug) && ! empty($model->{$model->slugSourceAttribute()})) {
                $model->slug = static::generateUniqueSlug(
                    (string) $model->{$model->slugSourceAttribute()},
                    $model
                );
            }
        });

        static::updating(function ($model): void {
            if ($model->isDirty($model->slugSourceAttribute()) && ! $model->isDirty('slug')) {
                $model->slug = static::generateUniqueSlug(
                    (string) $model->{$model->slugSourceAttribute()},
                    $model
                );
            }
        });
    }

    protected function slugSourceAttribute(): string
    {
        return 'title';
    }

    protected static function generateUniqueSlug(string $value, object $model): string
    {
        $slug = Str::slug($value);
        $original = $slug;
        $counter = 1;

        while (
            $model::query()
                ->where('slug', $slug)
                ->when($model->exists, fn ($query) => $query->whereKeyNot($model->getKey()))
                ->exists()
        ) {
            $slug = "{$original}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
