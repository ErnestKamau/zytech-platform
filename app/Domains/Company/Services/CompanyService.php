<?php

namespace App\Domains\Company\Services;

use App\Core\Contracts\CacheStore;
use App\Core\Enums\CompanyStatus;
use App\Core\Services\BaseService;
use App\Domains\Company\Data\CompanyData;
use App\Domains\Company\Events\CompanyUpdated;
use App\Domains\Company\Repositories\CompanyRepository;
use App\Domains\Company\Support\CompanyCache;
use App\Models\Award;
use App\Models\Certification;
use App\Models\Company;
use App\Models\CompanyStatistic;
use App\Models\Faq;
use Illuminate\Support\Collection;
use RuntimeException;

final class CompanyService extends BaseService
{
    public function __construct(
        private readonly CompanyRepository $companies,
        private readonly CacheStore $cache,
    ) {}

    public function current(): ?Company
    {
        return $this->companies->singleton();
    }

    public function profile(): ?CompanyData
    {
        $cached = $this->cache->get(CompanyCache::PROFILE);

        if (is_array($cached)) {
            return CompanyData::fromArray($cached);
        }

        $this->cache->forget(CompanyCache::PROFILE);

        $company = $this->companies->singleton();

        if ($company === null || ! $company->isPublished()) {
            return null;
        }

        $profile = CompanyData::fromArray([
            ...$company->toArray(),
            'is_published' => true,
        ]);

        $this->cache->put(CompanyCache::PROFILE, $profile->toArray(), now()->addHour());

        return $profile;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function create(array $attributes): Company
    {
        if ($this->companies->singleton() !== null) {
            throw new RuntimeException('A company profile already exists.');
        }

        $company = Company::query()->create($attributes);

        $this->forget();
        event(new CompanyUpdated($company->fresh()));

        return $company->refresh();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function updateProfile(array $attributes): Company
    {
        $company = $this->companies->singletonOrFail();
        $company->fill($attributes)->save();

        $this->forget();
        event(new CompanyUpdated($company->fresh()));

        return $company->refresh();
    }

    public function publish(): Company
    {
        $company = $this->companies->singletonOrFail();
        $company->forceFill([
            'status' => CompanyStatus::Published,
            'published_at' => $company->published_at ?? now(),
        ])->save();

        $this->forget();
        event(new CompanyUpdated($company->fresh()));

        return $company->refresh();
    }

    public function forget(): void
    {
        foreach (CompanyCache::all() as $key) {
            $this->cache->forget($key);
        }
    }

    /**
     * @return Collection<int, CompanyStatistic>
     */
    public function statistics(): Collection
    {
        return $this->rememberRelated(CompanyCache::STATISTICS, fn (Company $company): Collection => CompanyStatistic::query()
            ->where('company_id', $company->id)
            ->where('is_visible', true)
            ->orderBy('sort_order')
            ->get());
    }

    /**
     * @return Collection<int, Faq>
     */
    public function faqs(): Collection
    {
        return $this->rememberRelated(CompanyCache::FAQS, fn (Company $company): Collection => Faq::query()
            ->where('company_id', $company->id)
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->get());
    }

    /**
     * @return Collection<int, Certification>
     */
    public function certifications(): Collection
    {
        return $this->rememberRelated(CompanyCache::CERTIFICATIONS, fn (Company $company): Collection => Certification::query()
            ->where('company_id', $company->id)
            ->orderBy('sort_order')
            ->get());
    }

    /**
     * @return Collection<int, Award>
     */
    public function awards(): Collection
    {
        return $this->rememberRelated(CompanyCache::AWARDS, fn (Company $company): Collection => Award::query()
            ->where('company_id', $company->id)
            ->orderBy('sort_order')
            ->get());
    }

    /**
     * @param  callable(Company): Collection<int, mixed>  $callback
     * @return Collection<int, mixed>
     */
    private function rememberRelated(string $key, callable $callback): Collection
    {
        $company = $this->companies->singleton();

        if ($company === null) {
            return collect();
        }

        return CompanyCache::rememberCollection(
            $this->cache,
            $key,
            fn (): Collection => $callback($company),
        );
    }
}
