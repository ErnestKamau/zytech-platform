<?php

namespace App\Domains\Configuration\Events;

use App\Core\Events\BusinessEvent;
use App\Models\NavigationMenu;

final class NavigationUpdated extends BusinessEvent
{
    public function __construct(public NavigationMenu $menu) {}
}
