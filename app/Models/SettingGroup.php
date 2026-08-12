<?php

namespace App\Models;

use App\Core\Enums\SettingGroupType;
use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SettingGroup extends BaseModel
{
    use HasActivity;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'type',
        'description',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => SettingGroupType::class,
            'sort_order' => 'integer',
        ];
    }

    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class)->orderBy('sort_order');
    }
}
