<?php

namespace App\Domains\Communication\Actions;

use App\Core\Actions\BaseAction;
use App\Core\Enums\NotificationChannel;
use App\Domains\Communication\Services\CommunicationService;
use App\Models\User;

final class SendNotification extends BaseAction
{
    public function __construct(private readonly CommunicationService $communication) {}

    public function handle(mixed ...$arguments): mixed
    {
        /** @var string $type */
        $type = $arguments[0];
        /** @var string $email */
        $email = $arguments[1];
        /** @var User|null $user */
        $user = $arguments[2] ?? null;
        /** @var string|null $templateKey */
        $templateKey = $arguments[3] ?? null;
        /** @var array<string, string> $replacements */
        $replacements = $arguments[4] ?? [];
        /** @var list<NotificationChannel>|null $channels */
        $channels = $arguments[5] ?? null;

        $this->communication->notify(
            type: $type,
            recipientEmail: $email,
            user: $user,
            templateKey: $templateKey,
            replacements: $replacements,
            channels: $channels,
        );

        return null;
    }
}
