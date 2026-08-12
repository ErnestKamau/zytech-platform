<?php

namespace App\Domains\Configuration\Events;

use App\Core\Events\BusinessEvent;

final class SettingsUpdated extends BusinessEvent
{
    /**
     * @param  list<string>  $keys
     */
    public function __construct(public array $keys) {}
}
