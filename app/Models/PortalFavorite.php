<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PortalFavorite extends BaseModel
{
    /** @var list<string> */
    protected $fillable = [
        'client_id',
        'favoritable_type',
        'favoritable_id',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function favoritable(): MorphTo
    {
        return $this->morphTo();
    }
}
