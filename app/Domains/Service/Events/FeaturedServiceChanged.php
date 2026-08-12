<?php

namespace App\Domains\Service\Events;

use App\Core\Events\BusinessEvent;
use App\Models\Service;

final class FeaturedServiceChanged extends BusinessEvent
{
    public function __construct(public Service $service) {}
}
