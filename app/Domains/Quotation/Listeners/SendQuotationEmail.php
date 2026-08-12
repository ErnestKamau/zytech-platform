<?php

namespace App\Domains\Quotation\Listeners;

use App\Core\Enums\CommunicationNotificationType;
use App\Core\Enums\NotificationChannel;
use App\Core\Listeners\BaseListener;
use App\Domains\Communication\Services\CommunicationService;
use App\Domains\Quotation\Events\QuotationRequestSubmitted;
use App\Domains\Quotation\Events\QuotationSent;
use App\Infrastructure\Queue\QueueName;
use App\Models\User;

final class SendQuotationEmail extends BaseListener
{
    public string $queue = QueueName::MAIL;

    public function __construct(private readonly CommunicationService $communication) {}

    public function handle(QuotationRequestSubmitted|QuotationSent $event): void
    {
        if ($event instanceof QuotationRequestSubmitted) {
            $request = $event->request;
            $this->communication->notify(
                type: CommunicationNotificationType::QuotationSubmitted->value,
                recipientEmail: (string) $request->email,
                user: User::query()->where('email', $request->email)->first(),
                templateKey: 'quotation-submitted',
                replacements: [
                    'name' => (string) $request->full_name,
                    'reference' => (string) $request->reference_number,
                    'message' => 'Track your request anytime with your reference number.',
                ],
                channels: [NotificationChannel::Mail, NotificationChannel::Database],
                meta: ['quotation_request_id' => $request->id],
            );

            return;
        }

        $quotation = $event->quotation->loadMissing('request');
        $email = $quotation->request?->email;
        $name = $quotation->request?->full_name ?? 'there';

        if ($email === null || $email === '') {
            return;
        }

        $this->communication->notify(
            type: CommunicationNotificationType::QuotationSent->value,
            recipientEmail: $email,
            user: User::query()->where('email', $email)->first(),
            templateKey: 'quotation-sent',
            replacements: [
                'name' => (string) $name,
                'reference' => (string) $quotation->reference_number,
                'message' => 'Open the client portal or reply to this email if you have questions.',
            ],
            channels: [NotificationChannel::Mail, NotificationChannel::Database],
            meta: ['quotation_id' => $quotation->id],
        );
    }
}
