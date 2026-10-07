<?php

namespace App\Models;

use App\Core\Enums\DeliveryStatus;
use App\Core\Enums\NotificationChannel;
use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

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

    /**
     * Channels this subject was successfully sent through, deduped, newest first.
     * Used to surface "Sent via Email and WhatsApp on {date}" in document viewers.
     *
     * @return Collection<int, self>
     */
    public static function deliveredChannelsFor(string $metaKey, string $id): Collection
    {
        return static::query()
            ->where('meta->'.$metaKey, $id)
            ->where('status', DeliveryStatus::Sent)
            ->orderByDesc('created_at')
            ->get()
            ->unique('channel')
            ->values();
    }
}
