<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use App\Core\Traits\HasSlug;

class ArticleTag extends BaseModel
{
    use HasActivity;
    use HasSlug;

    /** @var list<string> */
    protected $fillable = ['name', 'slug'];

    protected function slugSourceAttribute(): string
    {
        return 'name';
    }
}
