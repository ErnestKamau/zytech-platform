<?php

namespace App\Domains\Client\Events;

use App\Core\Events\BusinessEvent;
use App\Models\Client;

final class ClientArchived extends BusinessEvent
{
    public function __construct(public Client $client) {}
}
