<?php

namespace App\Domains\Client\Events;

use App\Core\Events\BusinessEvent;
use App\Models\ClientCommunication;

final class CommunicationLogged extends BusinessEvent
{
    public function __construct(public ClientCommunication $communication) {}
}
