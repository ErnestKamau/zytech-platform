<?php

namespace App\Domains\Configuration\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Configuration\Data\BrandingData;
use App\Domains\Configuration\Services\BrandingService;

final class UpdateBranding extends BaseAction
{
    public function __construct(
        private readonly BrandingService $branding,
    ) {}

    public function handle(mixed ...$arguments): BrandingData
    {
        /** @var BrandingData $data */
        $data = $arguments[0];

        return $this->branding->update($data);
    }
}
