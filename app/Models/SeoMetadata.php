<?php

namespace App\Models;

use App\Core\Models\BaseModel;

class SeoMetadata extends BaseModel
{
    protected $table = 'seo_metadata';

    /** @var list<string> */
    protected $fillable = [
        'entity_type',
        'entity_id',
        'path',
        'title',
        'description',
        'canonical_url',
        'og_image',
        'structured_data',
        'seo_score',
    ];

    protected function casts(): array
    {
        return [
            'structured_data' => 'array',
            'seo_score' => 'integer',
        ];
    }
}
