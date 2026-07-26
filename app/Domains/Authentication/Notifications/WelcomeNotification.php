<?php

namespace App\Domains\Authentication\Notifications;

use App\Core\Notifications\BaseNotification;
use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;

final class WelcomeNotification extends BaseNotification
{
    public function toMail(object $notifiable): MailMessage
    {
        /** @var User $notifiable */
        return (new MailMessage)
            ->subject('Welcome to Zytech Contractors')
            ->greeting("Welcome, {$notifiable->name}!")
            ->line('Your account has been created successfully.')
            ->line('You can sign in to manage your quotations and project updates.')
            ->action('Sign in', url('/login'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'welcome',
            'message' => 'Welcome to Zytech Contractors',
        ];
    }
}
