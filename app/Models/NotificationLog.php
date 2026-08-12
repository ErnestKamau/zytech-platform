<?php

namespace App\Models;

use App\Core\Enums\DeliveryStatus;
use App\Core\Enums\NotificationChannel;
use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends BaseModel
{
    /** @var list<string> */
    protected $fillable = [
        'type',
        'channel',
        'status',
        'recipient',
        'user_id',
        'subject',
        'body',
        'meta',
        'error',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'channel' => NotificationChannel::class,
            'status' => DeliveryStatus::class,
            'meta' => 'array',
            'sent_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
