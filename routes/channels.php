<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, string $id): bool {
    return (string) $user->getAuthIdentifier() === (string) $id;
});

Broadcast::channel('admin.operations', function ($user): bool {
    return $user !== null;
});

Broadcast::channel('client.{clientId}', function ($user, string $clientId): bool {
    return (string) $user->getAuthIdentifier() === (string) $clientId;
});
