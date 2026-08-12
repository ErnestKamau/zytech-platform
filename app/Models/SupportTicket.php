<?php

namespace App\Models;

use App\Core\Enums\PriorityLevel;
use App\Core\Enums\TicketStatus;
use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends BaseModel
{
    use HasActivity;

    /** @var list<string> */
    protected $fillable = [
        'client_id',
        'reference_number',
        'subject',
        'body',
        'status',
        'priority',
        'assigned_to',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => TicketStatus::class,
            'priority' => PriorityLevel::class,
            'resolved_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(SupportReply::class)->orderBy('created_at');
    }
}
