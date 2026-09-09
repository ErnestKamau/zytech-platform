<?php

namespace App\Domains\Communication\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class StaffInviteMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public string $userName,
        public string $inviteUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'You’re invited to Zytech Admin');
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.auth.staff-invite',
            with: [
                'userName' => $this->userName !== '' ? $this->userName : 'there',
                'inviteUrl' => $this->inviteUrl,
            ],
        );
    }
}
