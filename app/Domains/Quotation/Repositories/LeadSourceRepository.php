<?php

namespace App\Domains\Quotation\Repositories;

use App\Models\LeadSource;
use Illuminate\Support\Collection;

final class LeadSourceRepository
{
    public function findBySlug(string $slug): ?LeadSource
    {
        return LeadSource::query()->where('slug', $slug)->where('is_active', true)->first();
    }

    /**
     * @return Collection<int, LeadSource>
     */
    public function active(): Collection
    {
        return LeadSource::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }
}
