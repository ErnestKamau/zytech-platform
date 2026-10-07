<?php

namespace App\Domains\Commerce\Listeners;

use App\Core\Enums\CommunicationNotificationType;
use App\Core\Enums\NotificationChannel;
use App\Core\Listeners\BaseListener;
use App\Domains\Commerce\Events\InvoiceIssued;
use App\Domains\Communication\Services\CommunicationService;
use App\Domains\Communication\Services\TwilioWhatsAppService;
use App\Infrastructure\Queue\QueueName;
use App\Models\User;
use Illuminate\Support\Facades\URL;

final class SendInvoiceWhatsApp extends BaseListener
{
    public string $queue = QueueName::MAIL;

    public function __construct(
        private readonly CommunicationService $communication,
        private readonly TwilioWhatsAppService $whatsapp,
    ) {}

    public function handle(InvoiceIssued $event): void
    {
        if (! $this->whatsapp->configured()) {
            return;
        }

        $invoice = $event->invoice->loadMissing(['client', 'quotation.request']);
        $phone = $invoice->client?->phone ?? $invoice->quotation?->request?->phone;

        if ($phone === null || $phone === '') {
            return;
        }

        $email = $invoice->client?->email ?? $invoice->quotation?->request?->email ?? '';
        $name = $invoice->client?->name ?? $invoice->quotation?->request?->full_name ?? 'there';

        $mediaUrl = URL::temporarySignedRoute(
            'documents.invoices.show',
            now()->addHours(24),
            ['invoice' => $invoice->id],
        );

        $this->communication->notify(
            type: CommunicationNotificationType::InvoiceIssued->value,
            recipientEmail: $email,
            user: $email !== '' ? User::query()->where('email', $email)->first() : null,
            client: $invoice->client,
            replacements: [
                'name' => (string) $name,
                'reference' => (string) $invoice->reference_number,
            ],
            channels: [NotificationChannel::WhatsApp],
            meta: [
                'invoice_id' => $invoice->id,
                'quotation_id' => $invoice->quotation_id,
                'client_id' => $invoice->client_id,
                'phone' => $phone,
                'whatsapp_media_url' => $mediaUrl,
                'whatsapp_template_vars' => [
                    '1' => (string) $name,
                    '2' => (string) $invoice->reference_number,
                ],
            ],
            subject: 'Invoice '.$invoice->reference_number,
            body: 'Your invoice '.$invoice->reference_number.' is ready.',
        );
    }
}
