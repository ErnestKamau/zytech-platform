<?php

namespace App\Domains\Quotation\Listeners;

use App\Core\Enums\CommunicationNotificationType;
use App\Core\Enums\NotificationChannel;
use App\Core\Listeners\BaseListener;
use App\Domains\Communication\Services\CommunicationService;
use App\Domains\Quotation\Events\QuotationAccepted;
use App\Domains\Quotation\Events\QuotationRejected;
use App\Domains\Quotation\Events\QuotationRequestSubmitted;
use App\Domains\Quotation\Events\QuotationSent;
use App\Infrastructure\Queue\QueueName;
use App\Models\User;

final class SendQuotationEmail extends BaseListener
{
    public string $queue = QueueName::MAIL;

    public function __construct(private readonly CommunicationService $communication) {}

    public function handle(QuotationRequestSubmitted|QuotationSent|QuotationAccepted|QuotationRejected $event): void
    {
        if ($event instanceof QuotationRequestSubmitted) {
            $request = $event->request;
            $this->communication->notify(
                type: CommunicationNotificationType::QuotationSubmitted->value,
                recipientEmail: (string) $request->email,
                user: User::query()->where('email', $request->email)->first(),
                client: $request->client,
                templateKey: 'quotation-submitted',
                replacements: [
                    'name' => (string) $request->full_name,
                    'reference' => (string) $request->reference_number,
                    'message' => 'Track your request anytime with your reference number.',
                ],
                channels: [NotificationChannel::Mail, NotificationChannel::Database],
                meta: [
                    'quotation_request_id' => $request->id,
                    'client_id' => $request->client_id,
                ],
            );

            return;
        }

        $quotation = $event->quotation->loadMissing(['request', 'client']);
        $email = $quotation->request?->email ?? $quotation->client?->email;
        $name = $quotation->request?->full_name ?? $quotation->client?->name ?? 'there';

        if ($email === null || $email === '') {
            return;
        }

        $user = User::query()->where('email', $email)->first();

        if ($event instanceof QuotationSent) {
            $this->communication->notify(
                type: CommunicationNotificationType::QuotationSent->value,
                recipientEmail: $email,
                user: $user,
                client: $quotation->client,
                templateKey: 'quotation-sent',
                replacements: [
                    'name' => (string) $name,
                    'reference' => (string) $quotation->reference_number,
                    'message' => 'Review, accept, reject, or request a revision in the client portal: '.route('portal.quotations'),
                ],
                channels: [NotificationChannel::Mail, NotificationChannel::Database],
                meta: [
                    'quotation_id' => $quotation->id,
                    'client_id' => $quotation->client_id,
                ],
            );

            return;
        }

        if ($event instanceof QuotationAccepted) {
            $this->communication->notify(
                type: CommunicationNotificationType::QuotationAccepted->value,
                recipientEmail: $email,
                user: $user,
                client: $quotation->client,
                templateKey: 'quotation-accepted',
                replacements: [
                    'name' => (string) $name,
                    'reference' => (string) $quotation->reference_number,
                    'message' => 'Our team will follow up on next steps.',
                ],
                channels: [NotificationChannel::Mail, NotificationChannel::Database],
                meta: [
                    'quotation_id' => $quotation->id,
                    'client_id' => $quotation->client_id,
                ],
            );

            return;
        }

        $this->communication->notify(
            type: CommunicationNotificationType::QuotationRejected->value,
            recipientEmail: $email,
            user: $user,
            client: $quotation->client,
            templateKey: 'quotation-rejected',
            replacements: [
                'name' => (string) $name,
                'reference' => (string) $quotation->reference_number,
                'message' => 'Contact us if you would like a revised quotation.',
            ],
            channels: [NotificationChannel::Mail, NotificationChannel::Database],
            meta: [
                'quotation_id' => $quotation->id,
                'client_id' => $quotation->client_id,
            ],
        );
    }
}
