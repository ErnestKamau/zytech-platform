<?php

namespace App\Domains\Company\Repositories;

use App\Models\Company;

final class CompanyRepository
{
    public function singleton(): ?Company
    {
        return Company::query()->first();
    }

    public function singletonOrFail(): Company
    {
        return Company::query()->firstOrFail();
    }
}
