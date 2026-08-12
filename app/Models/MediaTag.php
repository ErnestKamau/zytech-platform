<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use App\Core\Traits\HasSlug;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MediaTag extends BaseModel
{
    use HasActivity;
    use HasSlug;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    protected function slugSourceAttribute(): string
    {
        return 'name';
    }

    public function media(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'media_media_tag');
    }
}
