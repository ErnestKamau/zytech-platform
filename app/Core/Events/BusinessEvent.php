<?php

namespace App\Core\Events;

use Illuminate\Contracts\Events\ShouldDispatchAfterCommit;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Domain facts. When dispatched inside a DB transaction, listeners (including
 * queued notification listeners) run only after a successful commit.
 */
abstract class BusinessEvent implements ShouldDispatchAfterCommit
{
    use Dispatchable;
    use SerializesModels;
}
