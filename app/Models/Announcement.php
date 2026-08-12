<?php

namespace App\Models;

use App\Core\Enums\AnnouncementType;
use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Announcement extends BaseModel
{
    use HasActivity;

    /** @var list<string> */
    protected $fillable = [
        'title',
        'body',
        'type',
        'is_published',
        'show_on_website',
        'show_in_portal',
        'published_at',
        'expires_at',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'type' => AnnouncementType::class,
            'is_published' => 'boolean',
            'show_on_website' => 'boolean',
            'show_in_portal' => 'boolean',
            'published_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function reads(): HasMany
    {
        return $this->hasMany(AnnouncementRead::class);
    }
}
