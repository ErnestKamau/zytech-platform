<?php

namespace App\Models;

use App\Core\Enums\PortalNotificationType;
use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortalNotification extends BaseModel
{
    use HasActivity;

    /** @var list<string> */
    protected $fillable = [
        'client_id',
        'user_id',
        'type',
        'title',
        'body',
        'meta',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => PortalNotificationType::class,
            'meta' => 'array',
            'read_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isUnread(): bool
    {
        return $this->read_at === null;
    }
}
