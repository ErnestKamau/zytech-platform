<?php

namespace App\Domains\Communication\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Synchronous auth OTP mail — must not wait on the mail queue worker.
 */
final class AuthOtpMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public string $purpose,
        public string $code,
        public string $userName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: match ($this->purpose) {
            'enrollment' => 'Confirm two-factor authentication',
            'verification' => 'Verify your Zytech account',
            default => 'Your Zytech sign-in code',
        });
    }

    public function content(): Content
    {
        return new Content(
            view: match ($this->purpose) {
                'enrollment' => 'emails.auth.enrollment-code',
                'verification' => 'emails.auth.verification-code',
                default => 'emails.auth.login-code',
            },
            with: [
                'code' => $this->code,
                'userName' => $this->userName !== '' ? $this->userName : 'there',
            ],
        );
    }
}
