<?php

namespace App\Domains\Commerce\Listeners;

use App\Core\Listeners\BaseListener;
use App\Domains\Authentication\Events\UserLoggedIn;
use App\Domains\Commerce\Services\CartService;

final class MergeCartOnLogin extends BaseListener
{
    public function __construct(private readonly CartService $carts) {}

    public function handle(UserLoggedIn $event): void
    {
        $client = $this->carts->clientForUser($event->user);

        if ($client === null) {
            return;
        }

        $this->carts->mergeSessionIntoClient($client);
    }
}
