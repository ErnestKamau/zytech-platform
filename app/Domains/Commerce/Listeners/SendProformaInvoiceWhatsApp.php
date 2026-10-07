<?php

namespace App\Domains\Commerce\Listeners;

use App\Core\Enums\CommunicationNotificationType;
use App\Core\Enums\NotificationChannel;
use App\Core\Listeners\BaseListener;
use App\Domains\Commerce\Events\ProformaInvoiceIssued;
use App\Domains\Communication\Services\CommunicationService;
use App\Domains\Communication\Services\TwilioWhatsAppService;
use App\Infrastructure\Queue\QueueName;
use App\Models\User;
use Illuminate\Support\Facades\URL;

final class SendProformaInvoiceWhatsApp extends BaseListener
{
    public string $queue = QueueName::MAIL;

    public function __construct(
        private readonly CommunicationService $communication,
        private readonly TwilioWhatsAppService $whatsapp,
    ) {}

    public function handle(ProformaInvoiceIssued $event): void
    {
        if (! $this->whatsapp->configured()) {
            return;
        }

        $proforma = $event->proformaInvoice->loadMissing(['client', 'quotation.request']);
        $phone = $proforma->client?->phone ?? $proforma->quotation?->request?->phone;

        if ($phone === null || $phone === '') {
            return;
        }

        $email = $proforma->client?->email ?? $proforma->quotation?->request?->email ?? '';
        $name = $proforma->client?->name ?? $proforma->quotation?->request?->full_name ?? 'there';

        $mediaUrl = URL::temporarySignedRoute(
            'documents.proforma',
            now()->addHours(24),
            ['proformaInvoice' => $proforma->id],
        );

        $this->communication->notify(
            type: CommunicationNotificationType::ProformaInvoiceIssued->value,
            recipientEmail: $email,
            user: $email !== '' ? User::query()->where('email', $email)->first() : null,
            client: $proforma->client,
            replacements: [
                'name' => (string) $name,
                'reference' => (string) $proforma->reference_number,
            ],
            channels: [NotificationChannel::WhatsApp],
            meta: [
                'proforma_invoice_id' => $proforma->id,
                'quotation_id' => $proforma->quotation_id,
                'client_id' => $proforma->client_id,
                'phone' => $phone,
                'whatsapp_media_url' => $mediaUrl,
                'whatsapp_template_vars' => [
                    '1' => (string) $name,
                    '2' => (string) $proforma->reference_number,
                ],
            ],
            subject: 'Proforma invoice '.$proforma->reference_number,
            body: 'Your proforma invoice '.$proforma->reference_number.' is ready.',
        );
    }
}
