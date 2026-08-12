<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use App\Core\Traits\HasSlug;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ClientGroup extends BaseModel
{
    use HasActivity;
    use HasSlug;

    /** @var list<string> */
    protected $fillable = ['name', 'slug', 'description'];

    protected function slugSourceAttribute(): string
    {
        return 'name';
    }

    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class, 'client_group', 'client_group_id', 'client_id');
    }
}
