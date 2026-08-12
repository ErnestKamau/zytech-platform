<?php

namespace App\Domains\Company\Services;

use App\Core\Contracts\CacheStore;
use App\Core\Services\BaseService;
use App\Domains\Company\Events\PartnerAdded;
use App\Domains\Company\Repositories\CompanyRepository;
use App\Domains\Company\Repositories\PartnerRepository;
use App\Domains\Company\Support\CompanyCache;
use App\Models\Partner;
use Illuminate\Support\Collection;

final class PartnerService extends BaseService
{
    public function __construct(
        private readonly PartnerRepository $partners,
        private readonly CompanyRepository $companies,
        private readonly CacheStore $cache,
    ) {}

    /**
     * @return Collection<int, Partner>
     */
    public function published(): Collection
    {
        $company = $this->companies->singleton();

        if ($company === null) {
            return collect();
        }

        return CompanyCache::rememberCollection(
            $this->cache,
            CompanyCache::PARTNERS,
            fn (): Collection => $this->partners->publishedForCompany($company->id),
        );
    }

    public function archive(Partner $partner): Partner
    {
        $partner->forceFill([
            'archived_at' => now(),
            'is_published' => false,
        ])->save();

        $this->cache->forget(CompanyCache::PARTNERS);

        return $partner->refresh();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function add(array $attributes): Partner
    {
        $company = $this->companies->singletonOrFail();

        $partner = Partner::query()->create([
            ...$attributes,
            'company_id' => $company->id,
        ]);

        $this->cache->forget(CompanyCache::PARTNERS);
        event(new PartnerAdded($partner));

        return $partner;
    }
}
