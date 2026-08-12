<?php

namespace App\Domains\Configuration\Services;

use App\Core\Services\BaseService;
use App\Domains\Configuration\Data\BrandingData;
use App\Domains\Configuration\Events\BrandingUpdated;

final class BrandingService extends BaseService
{
    public function __construct(
        private readonly ConfigurationService $configuration,
    ) {}

    public function current(): BrandingData
    {
        return $this->configuration->branding();
    }

    public function update(BrandingData $data): BrandingData
    {
        $this->configuration->updateMany($data->toArray());
        event(new BrandingUpdated($data));

        return $this->configuration->branding();
    }
}
