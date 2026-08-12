<?php

namespace App\Domains\Communication\Notifications;

use App\Core\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;

final class HubDatabaseNotification extends BaseNotification
{
    /**
     * @param  array<string, mixed>  $meta
     */
    public function __construct(
        public string $type,
        public string $title,
        public string $body,
        public array $meta = [],
    ) {
        parent::__construct();
    }

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)->subject($this->title)->line($this->body);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'body' => $this->body,
            'meta' => $this->meta,
        ];
    }
}
