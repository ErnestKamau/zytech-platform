<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientStatusHistory extends BaseModel
{
    protected $table = 'client_status_history';

    /** @var list<string> */
    protected $fillable = ['client_id', 'from_status', 'to_status', 'notes', 'changed_by'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function changer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
