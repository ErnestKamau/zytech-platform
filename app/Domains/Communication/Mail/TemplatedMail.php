<?php

namespace App\Domains\Communication\Mail;

use App\Infrastructure\Queue\QueueName;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class TemplatedMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * @param  array{intent?: string, eyebrow?: string, heading?: string, preheader?: string, ctaUrl?: string, ctaLabel?: string}  $meta
     */
    public function __construct(
        public string $mailSubject,
        public string $mailBody,
        public array $meta = [],
    ) {
        $this->onQueue(QueueName::MAIL);
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->mailSubject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.message',
            with: [
                'mailSubject' => $this->mailSubject,
                'mailBody' => $this->mailBody,
                'intent' => $this->meta['intent'] ?? 'brand',
                'eyebrow' => $this->meta['eyebrow'] ?? null,
                'heading' => $this->meta['heading'] ?? $this->mailSubject,
                'preheader' => $this->meta['preheader'] ?? null,
                'ctaUrl' => $this->meta['ctaUrl'] ?? null,
                'ctaLabel' => $this->meta['ctaLabel'] ?? null,
            ],
        );
    }
}
