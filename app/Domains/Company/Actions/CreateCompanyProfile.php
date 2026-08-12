<?php

namespace App\Domains\Company\Actions;

use App\Core\Actions\BaseAction;
use App\Domains\Company\Services\CompanyService;
use App\Models\Company;

final class CreateCompanyProfile extends BaseAction
{
    public function __construct(
        private readonly CompanyService $companies,
    ) {}

    public function handle(mixed ...$arguments): Company
    {
        /** @var array<string, mixed> $attributes */
        $attributes = $arguments[0];

        return $this->companies->create($attributes);
    }
}
