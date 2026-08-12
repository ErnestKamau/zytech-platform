<?php

namespace App\Domains\Company\Events;

use App\Core\Events\BusinessEvent;
use App\Models\Company;

final class CompanyUpdated extends BusinessEvent
{
    public function __construct(public Company $company) {}
}
