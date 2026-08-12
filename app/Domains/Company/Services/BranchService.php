<?php

namespace App\Domains\Company\Services;

use App\Core\Contracts\CacheStore;
use App\Core\Services\BaseService;
use App\Domains\Company\Events\BranchCreated;
use App\Domains\Company\Repositories\BranchRepository;
use App\Domains\Company\Repositories\CompanyRepository;
use App\Domains\Company\Support\CompanyCache;
use App\Models\Branch;
use Illuminate\Support\Collection;

final class BranchService extends BaseService
{
    public function __construct(
        private readonly BranchRepository $branches,
        private readonly CompanyRepository $companies,
        private readonly CacheStore $cache,
    ) {}

    /**
     * @return Collection<int, Branch>
     */
    public function all(): Collection
    {
        $company = $this->companies->singleton();

        if ($company === null) {
            return collect();
        }

        return CompanyCache::rememberCollection(
            $this->cache,
            CompanyCache::BRANCHES,
            fn (): Collection => $this->branches->forCompany($company->id),
        );
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Branch
    {
        $company = $this->companies->singletonOrFail();

        $branch = $this->transaction(function () use ($company, $attributes): Branch {
            if (! empty($attributes['is_primary'])) {
                Branch::query()->where('company_id', $company->id)->update(['is_primary' => false]);
            }

            return Branch::query()->create([
                ...$attributes,
                'company_id' => $company->id,
            ]);
        });

        $this->cache->forget(CompanyCache::BRANCHES);
        event(new BranchCreated($branch));

        return $branch;
    }
}
