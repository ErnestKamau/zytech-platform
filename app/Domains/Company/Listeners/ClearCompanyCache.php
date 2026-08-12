<?php

namespace App\Domains\Company\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Company\Events\BranchCreated;
use App\Domains\Company\Events\CertificationUpdated;
use App\Domains\Company\Events\CompanyUpdated;
use App\Domains\Company\Events\PartnerAdded;
use App\Domains\Company\Events\TestimonialPublished;
use App\Domains\Company\Services\CompanyService;

final class ClearCompanyCache extends BaseListener
{
    public function __construct(
        private readonly CompanyService $companies,
    ) {}

    public function handle(
        CompanyUpdated|BranchCreated|PartnerAdded|TestimonialPublished|CertificationUpdated $event,
    ): void {
        $this->companies->forget();
    }
}
