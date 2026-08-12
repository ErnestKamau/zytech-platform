<?php

namespace App\Models;

use App\Core\Enums\MeetingType;
use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MeetingSlot extends BaseModel
{
    use HasActivity;

    /** @var list<string> */
    protected $fillable = [
        'meeting_type',
        'starts_at',
        'ends_at',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'meeting_type' => MeetingType::class,
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_available' => 'boolean',
        ];
    }

    public function requests(): HasMany
    {
        return $this->hasMany(MeetingRequest::class);
    }
}
