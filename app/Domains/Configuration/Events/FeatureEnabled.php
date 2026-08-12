<?php

namespace App\Domains\Configuration\Events;

use App\Core\Events\BusinessEvent;
use App\Models\FeatureFlag;

final class FeatureEnabled extends BusinessEvent
{
    public function __construct(public FeatureFlag $flag) {}
}
