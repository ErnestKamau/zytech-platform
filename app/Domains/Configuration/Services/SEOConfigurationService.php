<?php

namespace App\Domains\Configuration\Services;

use App\Core\Services\BaseService;
use App\Domains\Configuration\Data\SEOData;

final class SEOConfigurationService extends BaseService
{
    public function __construct(
        private readonly ConfigurationService $configuration,
    ) {}

    public function current(): SEOData
    {
        return $this->configuration->seo();
    }

    public function update(SEOData $data): SEOData
    {
        $this->configuration->updateMany($data->toArray());

        return $this->configuration->seo();
    }
}
