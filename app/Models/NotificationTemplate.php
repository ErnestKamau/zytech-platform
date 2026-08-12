<?php

namespace App\Models;

use App\Core\Enums\NotificationChannel;
use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;

class NotificationTemplate extends BaseModel
{
    use HasActivity;

    /** @var list<string> */
    protected $fillable = [
        'key',
        'name',
        'channel',
        'subject',
        'body',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'channel' => NotificationChannel::class,
            'is_active' => 'boolean',
        ];
    }
}
