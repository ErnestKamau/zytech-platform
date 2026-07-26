<?php

namespace App\Domains\Authentication\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Authentication\Events\AccountLocked;
use App\Domains\Authentication\Events\UserLoggedIn;
use App\Domains\Authentication\Events\UserLoggedOut;
use App\Infrastructure\Queue\QueueName;
use Illuminate\Support\Facades\Log;

/**
 * Placeholder for Reverb broadcast of authentication events.
 * Full private-channel broadcasting lands with the Communication Hub phase;
 * this listener keeps the wiring discoverable and logs for observability.
 */
final class BroadcastAuthenticationEvent extends BaseListener
{
    public string $queue = QueueName::BROADCAST;

    public function handle(UserLoggedIn|UserLoggedOut|AccountLocked $event): void
    {
        Log::info('authentication.broadcast', [
            'event' => class_basename($event),
            'user_id' => $event->user->id,
        ]);
    }
}
