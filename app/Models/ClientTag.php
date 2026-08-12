<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use App\Core\Traits\HasSlug;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ClientTag extends BaseModel
{
    use HasActivity;
    use HasSlug;

    /** @var list<string> */
    protected $fillable = ['name', 'slug'];

    protected function slugSourceAttribute(): string
    {
        return 'name';
    }

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class, 'client_tag', 'client_tag_id', 'client_id');
    }
}
