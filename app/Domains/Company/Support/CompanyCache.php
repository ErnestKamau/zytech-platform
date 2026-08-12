<?php

namespace App\Domains\Company\Support;

use App\Core\Contracts\CacheStore;
use Illuminate\Support\Collection;

final class CompanyCache
{
    public const PROFILE = 'company.profile';

    public const LEADERSHIP = 'company.leadership';

    public const BRANCHES = 'company.branches';

    public const TESTIMONIALS = 'company.testimonials';

    public const PARTNERS = 'company.partners';

    public const STATISTICS = 'company.statistics';

    public const CERTIFICATIONS = 'company.certifications';

    public const FAQS = 'company.faqs';

    public const AWARDS = 'company.awards';

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return [
            self::PROFILE,
            self::LEADERSHIP,
            self::BRANCHES,
            self::TESTIMONIALS,
            self::PARTNERS,
            self::STATISTICS,
            self::CERTIFICATIONS,
            self::FAQS,
            self::AWARDS,
        ];
    }

    /**
     * @param  callable(): Collection<int, mixed>  $callback
     * @return Collection<int, mixed>
     */
    public static function rememberCollection(CacheStore $cache, string $key, callable $callback): Collection
    {
        $cached = $cache->get($key);

        if ($cached instanceof Collection) {
            return $cached;
        }

        $cache->forget($key);
        $value = $callback();
        $cache->put($key, $value, now()->addHour());

        return $value instanceof Collection ? $value : collect($value);
    }
}
