<?php

namespace App\Models;

use App\Core\Enums\ConversationStatus;
use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PortalConversation extends BaseModel
{
    use HasActivity;

    /** @var list<string> */
    protected $fillable = [
        'client_id',
        'subject',
        'status',
        'assigned_to',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ConversationStatus::class,
            'last_message_at' => 'datetime',
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

    public function messages(): HasMany
    {
        return $this->hasMany(PortalMessage::class)->orderBy('created_at');
    }
}
