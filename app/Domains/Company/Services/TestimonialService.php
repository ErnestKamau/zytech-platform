<?php

namespace App\Domains\Company\Services;

use App\Core\Contracts\CacheStore;
use App\Core\Enums\CompanyStatus;
use App\Core\Services\BaseService;
use App\Domains\Company\Events\TestimonialPublished;
use App\Domains\Company\Repositories\CompanyRepository;
use App\Domains\Company\Support\CompanyCache;
use App\Models\Testimonial;
use Illuminate\Support\Collection;

final class TestimonialService extends BaseService
{
    public function __construct(
        private readonly CompanyRepository $companies,
        private readonly CacheStore $cache,
    ) {}

    /**
     * @return Collection<int, Testimonial>
     */
    public function published(): Collection
    {
        $company = $this->companies->singleton();

        if ($company === null) {
            return collect();
        }

        return CompanyCache::rememberCollection(
            $this->cache,
            CompanyCache::TESTIMONIALS,
            fn (): Collection => Testimonial::query()
                ->where('company_id', $company->id)
                ->published()
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->get(),
        );
    }

    public function publish(Testimonial $testimonial): Testimonial
    {
        $testimonial->forceFill([
            'status' => CompanyStatus::Published,
            'published_at' => $testimonial->published_at ?? now(),
        ])->save();

        $this->cache->forget(CompanyCache::TESTIMONIALS);
        event(new TestimonialPublished($testimonial->fresh()));

        return $testimonial->refresh();
    }
}
