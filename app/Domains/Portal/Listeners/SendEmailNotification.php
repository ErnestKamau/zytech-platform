<?php

namespace App\Domains\Portal\Listeners;

use App\Core\Enums\CommunicationNotificationType;
use App\Core\Enums\NotificationChannel;
use App\Core\Listeners\BaseListener;
use App\Domains\Communication\Services\CommunicationService;
use App\Domains\Portal\Events\MeetingScheduled;
use App\Domains\Portal\Events\MessageSent;
use App\Domains\Portal\Events\NotificationCreated;
use App\Domains\Portal\Events\TicketOpened;
use App\Infrastructure\Queue\QueueName;

final class SendEmailNotification extends BaseListener
{
    public string $queue = QueueName::MAIL;

    public function __construct(private readonly CommunicationService $communication) {}

    public function handle(MessageSent|TicketOpened|MeetingScheduled|NotificationCreated $event): void
    {
        if ($event instanceof MessageSent) {
            $conversation = $event->message->conversation?->loadMissing('client');
            $client = $conversation?->client;
            $email = $client?->email;

            if ($email === null) {
                return;
            }

            $this->communication->notify(
                type: CommunicationNotificationType::PortalMessage->value,
                recipientEmail: $email,
                user: $client?->user,
                templateKey: 'portal-message',
                replacements: [
                    'name' => (string) ($client?->name ?? 'there'),
                    'message' => (string) $event->message->body,
                ],
                channels: [NotificationChannel::Mail],
            );

            return;
        }

        if ($event instanceof TicketOpened) {
            $ticket = $event->ticket->loadMissing('client');
            $email = $ticket->client?->email;
            if ($email === null) {
                return;
            }

            $this->communication->notify(
                type: CommunicationNotificationType::SupportTicket->value,
                recipientEmail: $email,
                user: $ticket->client?->user,
                replacements: [
                    'name' => (string) ($ticket->client?->name ?? 'there'),
                    'message' => $ticket->reference_number.': '.$ticket->subject,
                ],
                subject: 'Support ticket '.$ticket->reference_number,
                body: "We opened your support ticket {$ticket->reference_number}.\n\n{$ticket->subject}\n\n{$ticket->body}",
                channels: [NotificationChannel::Mail],
            );

            return;
        }

        if ($event instanceof MeetingScheduled) {
            $meeting = $event->meeting->loadMissing('client');
            $email = $meeting->client?->email;
            if ($email === null) {
                return;
            }

            $this->communication->notify(
                type: CommunicationNotificationType::MeetingScheduled->value,
                recipientEmail: $email,
                user: $meeting->client?->user,
                replacements: [
                    'name' => (string) ($meeting->client?->name ?? 'there'),
                    'message' => $meeting->meeting_type->label(),
                ],
                subject: 'Meeting request received',
                body: 'Your '.$meeting->meeting_type->label().' request has been received.',
                channels: [NotificationChannel::Mail],
            );
        }
    }
}
