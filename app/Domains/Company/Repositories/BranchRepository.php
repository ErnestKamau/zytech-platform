<?php

namespace App\Domains\Company\Repositories;

use App\Models\Branch;
use Illuminate\Support\Collection;

final class BranchRepository
{
    /**
     * @return Collection<int, Branch>
     */
    public function forCompany(string $companyId): Collection
    {
        return Branch::query()
            ->where('company_id', $companyId)
            ->orderByDesc('is_primary')
            ->orderBy('sort_order')
            ->get();
    }
}
