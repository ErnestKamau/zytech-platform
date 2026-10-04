<?php

namespace App\Domains\Commerce\Listeners;

use App\Core\Enums\CommunicationNotificationType;
use App\Core\Enums\NotificationChannel;
use App\Core\Listeners\BaseListener;
use App\Domains\Commerce\Events\ProformaInvoiceIssued;
use App\Domains\Communication\Services\CommunicationService;
use App\Infrastructure\Queue\QueueName;
use App\Models\User;

final class SendProformaInvoiceEmail extends BaseListener
{
    public string $queue = QueueName::MAIL;

    public function __construct(private readonly CommunicationService $communication) {}

    public function handle(ProformaInvoiceIssued $event): void
    {
        $proforma = $event->proformaInvoice->loadMissing(['client', 'quotation.request']);
        $email = $proforma->client?->email ?? $proforma->quotation?->request?->email;
        $name = $proforma->client?->name ?? $proforma->quotation?->request?->full_name ?? 'there';

        if ($email === null || $email === '') {
            return;
        }

        $this->communication->notify(
            type: CommunicationNotificationType::ProformaInvoiceIssued->value,
            recipientEmail: $email,
            user: User::query()->where('email', $email)->first(),
            client: $proforma->client,
            templateKey: 'proforma-invoice-issued',
            replacements: [
                'name' => (string) $name,
                'reference' => (string) $proforma->reference_number,
                'message' => 'Review your proforma invoice in the client portal: '.route('portal.quotations'),
            ],
            channels: [NotificationChannel::Mail, NotificationChannel::Database],
            meta: [
                'proforma_invoice_id' => $proforma->id,
                'quotation_id' => $proforma->quotation_id,
                'client_id' => $proforma->client_id,
            ],
        );
    }
}
