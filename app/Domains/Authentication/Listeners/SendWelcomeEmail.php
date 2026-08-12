<?php

namespace App\Domains\Authentication\Listeners;

use App\Core\Enums\CommunicationNotificationType;
use App\Core\Enums\NotificationChannel;
use App\Core\Listeners\BaseListener;
use App\Domains\Authentication\Events\UserRegistered;
use App\Domains\Communication\Services\CommunicationService;
use App\Infrastructure\Queue\QueueName;

final class SendWelcomeEmail extends BaseListener
{
    public string $queue = QueueName::MAIL;

    public function __construct(private readonly CommunicationService $communication) {}

    public function handle(UserRegistered $event): void
    {
        $user = $event->user;

        $this->communication->notify(
            type: CommunicationNotificationType::Welcome->value,
            recipientEmail: $user->email,
            user: $user,
            templateKey: 'welcome',
            replacements: [
                'name' => $user->name,
                'message' => 'You can sign in anytime at '.url('/login'),
            ],
            channels: [NotificationChannel::Mail, NotificationChannel::Database],
        );
    }
}
