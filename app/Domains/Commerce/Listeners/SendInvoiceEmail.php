<?php

namespace App\Domains\Commerce\Listeners;

use App\Core\Enums\CommunicationNotificationType;
use App\Core\Enums\NotificationChannel;
use App\Core\Listeners\BaseListener;
use App\Domains\Commerce\Events\InvoiceIssued;
use App\Domains\Communication\Services\CommunicationService;
use App\Infrastructure\Queue\QueueName;
use App\Models\User;

final class SendInvoiceEmail extends BaseListener
{
    public string $queue = QueueName::MAIL;

    public function __construct(private readonly CommunicationService $communication) {}

    public function handle(InvoiceIssued $event): void
    {
        $invoice = $event->invoice->loadMissing(['client', 'quotation.request']);
        $email = $invoice->client?->email ?? $invoice->quotation?->request?->email;
        $name = $invoice->client?->name ?? $invoice->quotation?->request?->full_name ?? 'there';

        if ($email === null || $email === '') {
            return;
        }

        $this->communication->notify(
            type: CommunicationNotificationType::InvoiceIssued->value,
            recipientEmail: $email,
            user: User::query()->where('email', $email)->first(),
            client: $invoice->client,
            templateKey: 'invoice-issued',
            replacements: [
                'name' => (string) $name,
                'reference' => (string) $invoice->reference_number,
                'message' => 'View, download, and pay your invoice in the client portal: '.route('portal.invoices'),
            ],
            channels: [NotificationChannel::Mail, NotificationChannel::Database],
            meta: [
                'invoice_id' => $invoice->id,
                'quotation_id' => $invoice->quotation_id,
                'client_id' => $invoice->client_id,
            ],
        );
    }
}
