<?php

namespace App\Core\Listeners;

use App\Infrastructure\Queue\QueueName;
use Illuminate\Contracts\Queue\ShouldQueue;

abstract class BaseListener implements ShouldQueue
{
    public string $queue = QueueName::DEFAULT;

    public int $tries = 3;
}
