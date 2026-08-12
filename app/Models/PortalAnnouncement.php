<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;

class PortalAnnouncement extends BaseModel
{
    use HasActivity;

    /** @var list<string> */
    protected $fillable = [
        'title',
        'body',
        'is_published',
        'published_at',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }
}
