<?php

namespace App\Models;

use App\Core\Enums\ClientTimelineEvent;
use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientTimeline extends BaseModel
{
    protected $table = 'client_timelines';

    /** @var list<string> */
    protected $fillable = ['client_id', 'event_type', 'title', 'description', 'occurred_at', 'meta'];

    protected function casts(): array
    {
        return [
            'event_type' => ClientTimelineEvent::class,
            'occurred_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
