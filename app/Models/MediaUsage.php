<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MediaUsage extends BaseModel
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'media_id',
        'usable_type',
        'usable_id',
        'context',
    ];

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    public function usable(): MorphTo
    {
        return $this->morphTo();
    }
}
