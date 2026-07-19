<?php

namespace App\Core\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class SystemEvent
{
    use Dispatchable;
    use SerializesModels;
}
