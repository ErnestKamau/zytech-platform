<?php

namespace App\Models;

use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;

class SeoRedirect extends BaseModel
{
    use HasActivity;

    /** @var list<string> */
    protected $fillable = [
        'from_path',
        'to_path',
        'status_code',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'status_code' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
