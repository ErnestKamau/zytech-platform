<?php

namespace App\Core\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class BusinessEvent
{
    use Dispatchable;
    use SerializesModels;
}
