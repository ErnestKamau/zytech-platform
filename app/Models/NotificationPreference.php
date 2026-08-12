<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends BaseModel
{
    /** @var list<string> */
    protected $fillable = [
        'user_id',
        'mail_enabled',
        'database_enabled',
        'broadcast_enabled',
        'marketing_enabled',
    ];

    protected function casts(): array
    {
        return [
            'mail_enabled' => 'boolean',
            'database_enabled' => 'boolean',
            'broadcast_enabled' => 'boolean',
            'marketing_enabled' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
