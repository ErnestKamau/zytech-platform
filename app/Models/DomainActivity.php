<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Immutable domain activity row (table: activities).
 * Distinct from Spatie activity_log used by HasActivity.
 */
class DomainActivity extends Model
{
    use HasUuids;

    public $incrementing = false;

    public $timestamps = false;

    protected $keyType = 'string';

    protected $table = 'activities';

    /** @var list<string> */
    protected $fillable = [
        'subject_type',
        'subject_id',
        'actor_id',
        'event',
        'properties',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'properties' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function delete(): ?bool
    {
        return false;
    }

    public function update(array $attributes = [], array $options = []): bool
    {
        return false;
    }
}
