<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientNote extends BaseModel
{
    use HasActivity;

    /** @var list<string> */
    protected $fillable = ['client_id', 'body', 'is_internal', 'author_id'];

    protected function casts(): array
    {
        return ['is_internal' => 'boolean'];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
