<?php

namespace App\Domains\Configuration\Repositories;

use App\Models\Setting;
use Illuminate\Support\Collection;

final class SettingRepository
{
    /**
     * @return array<string, mixed>
     */
    public function allKeyed(): array
    {
        return Setting::query()
            ->with('group')
            ->orderBy('sort_order')
            ->get()
            ->mapWithKeys(fn (Setting $setting): array => [$setting->key => $setting->typedValue()])
            ->all();
    }

    public function findByKey(string $key): ?Setting
    {
        return Setting::query()->where('key', $key)->first();
    }

    public function updateValue(string $key, mixed $value): Setting
    {
        $setting = Setting::query()->where('key', $key)->firstOrFail();
        $setting->setTypedValue($value);
        $setting->save();

        return $setting->refresh();
    }

    /**
     * @return Collection<int, Setting>
     */
    public function forGroup(string $groupSlug): Collection
    {
        return Setting::query()
            ->whereHas('group', fn ($query) => $query->where('slug', $groupSlug))
            ->orderBy('sort_order')
            ->get();
    }
}
