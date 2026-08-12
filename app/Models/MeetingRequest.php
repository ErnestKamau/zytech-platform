<?php

namespace App\Models;

use App\Core\Enums\MeetingStatus;
use App\Core\Enums\MeetingType;
use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingRequest extends BaseModel
{
    use HasActivity;

    /** @var list<string> */
    protected $fillable = [
        'client_id',
        'meeting_slot_id',
        'meeting_type',
        'status',
        'scheduled_at',
        'location',
        'notes',
        'assigned_to',
    ];

    protected function casts(): array
    {
        return [
            'meeting_type' => MeetingType::class,
            'status' => MeetingStatus::class,
            'scheduled_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function slot(): BelongsTo
    {
        return $this->belongsTo(MeetingSlot::class, 'meeting_slot_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
