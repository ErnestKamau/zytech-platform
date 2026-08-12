<?php

namespace App\Models;

use App\Core\Enums\FeatureStatus;
use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;

class FeatureFlag extends BaseModel
{
    use HasActivity;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'key',
        'name',
        'description',
        'status',
        'metadata',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => FeatureStatus::class,
            'metadata' => 'array',
        ];
    }

    public function isEnabled(): bool
    {
        return $this->status === FeatureStatus::Enabled;
    }
}
