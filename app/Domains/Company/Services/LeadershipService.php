<?php

namespace App\Domains\Company\Services;

use App\Core\Contracts\CacheStore;
use App\Core\Services\BaseService;
use App\Domains\Company\Repositories\CompanyRepository;
use App\Domains\Company\Support\CompanyCache;
use App\Models\LeadershipMember;
use Illuminate\Support\Collection;

final class LeadershipService extends BaseService
{
    public function __construct(
        private readonly CompanyRepository $companies,
        private readonly CacheStore $cache,
    ) {}

    /**
     * @return Collection<int, LeadershipMember>
     */
    public function visible(): Collection
    {
        $company = $this->companies->singleton();

        if ($company === null) {
            return collect();
        }

        return CompanyCache::rememberCollection(
            $this->cache,
            CompanyCache::LEADERSHIP,
            fn (): Collection => LeadershipMember::query()
                ->where('company_id', $company->id)
                ->where('is_visible', true)
                ->orderBy('sort_order')
                ->get(),
        );
    }
}
