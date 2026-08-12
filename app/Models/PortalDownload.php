<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortalDownload extends BaseModel
{
    /** @var list<string> */
    protected $fillable = [
        'client_id',
        'user_id',
        'client_document_id',
        'label',
        'stored_path',
        'downloaded_at',
    ];

    protected function casts(): array
    {
        return [
            'downloaded_at' => 'datetime',
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

    public function document(): BelongsTo
    {
        return $this->belongsTo(ClientDocument::class, 'client_document_id');
    }
}
