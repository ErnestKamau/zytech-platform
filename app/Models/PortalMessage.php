<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortalMessage extends BaseModel
{
    use HasActivity;

    /** @var list<string> */
    protected $fillable = [
        'portal_conversation_id',
        'user_id',
        'body',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(PortalConversation::class, 'portal_conversation_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
