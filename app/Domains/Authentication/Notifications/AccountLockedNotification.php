<?php

namespace App\Domains\Authentication\Notifications;

use App\Core\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;

final class AccountLockedNotification extends BaseNotification
{
    public function __construct(private readonly string $reason)
    {
        parent::__construct();
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Zytech account was locked')
            ->line('Your account has been locked for security reasons.')
            ->line('Reason: '.$this->reason)
            ->line('Contact support if you believe this was a mistake.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'account_locked',
            'message' => 'Your account was locked: '.$this->reason,
        ];
    }
}
