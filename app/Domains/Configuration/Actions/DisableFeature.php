<?php

namespace App\Domains\Configuration\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Configuration\Services\FeatureFlagService;
use App\Models\FeatureFlag;

final class DisableFeature extends BaseAction
{
    public function __construct(
        private readonly FeatureFlagService $flags,
    ) {}

    public function handle(mixed ...$arguments): FeatureFlag
    {
        return $this->flags->disable((string) $arguments[0]);
    }
}
