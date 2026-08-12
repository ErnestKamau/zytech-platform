<?php

namespace App\Domains\Configuration\Services;

use App\Core\Contracts\CacheStore;
use App\Core\Enums\FeatureStatus;
use App\Core\Services\BaseService;
use App\Domains\Configuration\Events\FeatureDisabled;
use App\Domains\Configuration\Events\FeatureEnabled;
use App\Domains\Configuration\Repositories\FeatureFlagRepository;
use App\Domains\Configuration\Support\ConfigurationCache;
use App\Models\FeatureFlag;

final class FeatureFlagService extends BaseService
{
    public function __construct(
        private readonly FeatureFlagRepository $flags,
        private readonly CacheStore $cache,
    ) {}

    public function enabled(string $key): bool
    {
        return (bool) ($this->all()[$key] ?? false);
    }

    /**
     * @return array<string, bool>
     */
    public function all(): array
    {
        return $this->cache->remember(
            ConfigurationCache::FEATURE_FLAGS,
            now()->addHour(),
            fn (): array => $this->flags->all()
                ->mapWithKeys(fn (FeatureFlag $flag): array => [$flag->key => $flag->isEnabled()])
                ->all(),
        );
    }

    public function enable(string $key): FeatureFlag
    {
        return $this->setStatus($key, FeatureStatus::Enabled);
    }

    public function disable(string $key): FeatureFlag
    {
        return $this->setStatus($key, FeatureStatus::Disabled);
    }

    public function forget(): void
    {
        $this->cache->forget(ConfigurationCache::FEATURE_FLAGS);
    }

    private function setStatus(string $key, FeatureStatus $status): FeatureFlag
    {
        $flag = $this->flags->findByKey($key);

        if ($flag === null) {
            throw new \InvalidArgumentException("Unknown feature flag [{$key}].");
        }

        $flag->forceFill(['status' => $status])->save();
        $this->forget();

        if ($status === FeatureStatus::Enabled) {
            event(new FeatureEnabled($flag->fresh()));
        } else {
            event(new FeatureDisabled($flag->fresh()));
        }

        return $flag->refresh();
    }
}
