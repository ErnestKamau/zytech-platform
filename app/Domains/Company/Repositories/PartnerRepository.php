<?php

namespace App\Domains\Company\Repositories;

use App\Models\Partner;
use Illuminate\Support\Collection;

final class PartnerRepository
{
    /**
     * @return Collection<int, Partner>
     */
    public function publishedForCompany(string $companyId): Collection
    {
        return Partner::query()
            ->where('company_id', $companyId)
            ->where('is_published', true)
            ->whereNull('archived_at')
            ->orderBy('sort_order')
            ->get();
    }
}
