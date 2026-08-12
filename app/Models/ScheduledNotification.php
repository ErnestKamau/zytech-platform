<?php

namespace App\Models;

use App\Core\Enums\DeliveryStatus;
use App\Core\Enums\NotificationChannel;
use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;

class ScheduledNotification extends BaseModel
{
    use HasActivity;

    /** @var list<string> */
    protected $fillable = [
        'type',
        'channel',
        'recipient',
        'subject',
        'body',
        'send_at',
        'status',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'channel' => NotificationChannel::class,
            'status' => DeliveryStatus::class,
            'send_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }
}
