<?php

namespace App\Models;

use App\Core\Enums\CommunicationMethod;
use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientCommunication extends BaseModel
{
    use HasActivity;

    /** @var list<string> */
    protected $fillable = ['client_id', 'channel', 'subject', 'summary', 'occurred_at', 'logged_by'];

    protected function casts(): array
    {
        return [
            'channel' => CommunicationMethod::class,
            'occurred_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function logger(): BelongsTo
    {
        return $this->belongsTo(User::class, 'logged_by');
    }
}
