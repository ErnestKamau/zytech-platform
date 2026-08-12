<?php

namespace App\Domains\Configuration\Repositories;

use App\Models\FeatureFlag;
use Illuminate\Support\Collection;

final class FeatureFlagRepository
{
    /**
     * @return Collection<int, FeatureFlag>
     */
    public function all(): Collection
    {
        return FeatureFlag::query()->orderBy('name')->get();
    }

    public function findByKey(string $key): ?FeatureFlag
    {
        return FeatureFlag::query()->where('key', $key)->first();
    }
}
