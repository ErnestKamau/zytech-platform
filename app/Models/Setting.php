<?php

namespace App\Models;

use App\Core\Enums\SettingType;
use App\Core\Models\BaseModel;
use App\Core\Traits\HasActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends BaseModel
{
    use HasActivity;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'setting_group_id',
        'key',
        'label',
        'type',
        'value',
        'is_public',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => SettingType::class,
            'is_public' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(SettingGroup::class, 'setting_group_id');
    }

    public function typedValue(): mixed
    {
        $raw = $this->value;

        return match ($this->type) {
            SettingType::Boolean => filter_var($raw, FILTER_VALIDATE_BOOLEAN),
            SettingType::Integer => $raw === null || $raw === '' ? 0 : (int) $raw,
            SettingType::Json => is_string($raw) ? json_decode($raw, true) : $raw,
            default => $raw,
        };
    }

    public function setTypedValue(mixed $value): void
    {
        $this->value = match ($this->type) {
            SettingType::Boolean => $value ? '1' : '0',
            SettingType::Integer => (string) (int) $value,
            SettingType::Json => is_string($value) ? $value : json_encode($value),
            default => $value === null ? null : (string) $value,
        };
    }
}
