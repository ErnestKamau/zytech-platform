<?php

namespace App\Domains\Company\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Company\Services\CompanyService;
use App\Models\Company;

final class PublishCompanyProfile extends BaseAction
{
    public function __construct(
        private readonly CompanyService $companies,
    ) {}

    public function handle(mixed ...$arguments): Company
    {
        return $this->companies->publish();
    }
}
