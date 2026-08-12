<?php

namespace App\Domains\Company\Events;

use App\Core\Events\BusinessEvent;
use App\Models\Branch;

final class BranchCreated extends BusinessEvent
{
    public function __construct(public Branch $branch) {}
}
