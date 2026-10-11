<?php

namespace App\Models;

use App\Core\Models\BaseModel;

class EtimsSetting extends BaseModel
{
    /** @var list<string> */
    protected $fillable = [
        'enabled',
        'environment',
        'base_url_override',
        'device_serial',
        'default_item_classification_code',
        'default_item_code',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
        ];
    }
}
