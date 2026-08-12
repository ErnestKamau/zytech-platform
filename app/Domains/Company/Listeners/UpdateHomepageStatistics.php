<?php

namespace App\Domains\Company\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Company\Events\CompanyUpdated;
use App\Domains\Company\Events\TestimonialPublished;
use App\Domains\Company\Services\CompanyService;

final class UpdateHomepageStatistics extends BaseListener
{
    public function __construct(
        private readonly CompanyService $companies,
    ) {}

    public function handle(CompanyUpdated|TestimonialPublished $event): void
    {
        $this->companies->forget();
        $this->companies->statistics();
    }
}
