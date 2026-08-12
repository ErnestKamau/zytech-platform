<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientPreference extends BaseModel
{
    /** @var list<string> */
    protected $fillable = [
        'client_id',
        'email_notifications',
        'sms_notifications',
        'whatsapp_notifications',
        'marketing_opt_in',
    ];

    protected function casts(): array
    {
        return [
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'whatsapp_notifications' => 'boolean',
            'marketing_opt_in' => 'boolean',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
