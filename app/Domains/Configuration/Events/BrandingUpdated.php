<?php

namespace App\Domains\Configuration\Events;

use App\Core\Events\BusinessEvent;
use App\Domains\Configuration\Data\BrandingData;

final class BrandingUpdated extends BusinessEvent
{
    public function __construct(public BrandingData $branding) {}
}
