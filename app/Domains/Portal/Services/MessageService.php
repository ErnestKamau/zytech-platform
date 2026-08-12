<?php

namespace App\Domains\Portal\Services;

use App\Core\Enums\ConversationStatus;
use App\Core\Enums\PortalNotificationType;
use App\Core\Services\BaseService;
use App\Domains\Portal\Events\MessageSent;
use App\Domains\Portal\Repositories\MessageRepository;
use App\Models\Client;
use App\Models\PortalConversation;
use App\Models\PortalMessage;
use App\Models\User;
use Illuminate\Support\Collection;

final class MessageService extends BaseService
{
    public function __construct(
        private readonly MessageRepository $messages,
        private readonly NotificationService $notifications,
        private readonly DashboardService $dashboard,
    ) {}

    /**
     * @return Collection<int, PortalConversation>
     */
    public function conversations(Client $client): Collection
    {
        return $this->messages->forClient($client);
    }

    public function open(Client $client, string $subject, string $body, User $author): PortalConversation
    {
        $conversation = PortalConversation::query()->create([
            'client_id' => $client->id,
            'subject' => $subject,
            'status' => ConversationStatus::Open,
            'last_message_at' => now(),
        ]);

        $this->post($conversation, $author, $body);

        return $conversation->fresh(['messages.author']);
    }

    public function send(PortalConversation $conversation, User $author, string $body): PortalMessage
    {
        return $this->post($conversation, $author, $body);
    }

    private function post(PortalConversation $conversation, User $author, string $body): PortalMessage
    {
        $conversation->loadMissing('client');

        $message = PortalMessage::query()->create([
            'portal_conversation_id' => $conversation->id,
            'user_id' => $author->id,
            'body' => $body,
            'read_at' => $author->id === $conversation->client?->user_id ? now() : null,
        ]);

        $conversation->forceFill(['last_message_at' => now()])->save();

        if ($author->id === $conversation->client?->user_id && $conversation->client !== null) {
            $this->notifications->create(
                $conversation->client,
                PortalNotificationType::Message,
                'Message sent',
                'Your message in “'.$conversation->subject.'” was delivered to Zytech.',
            );
        }

        event(new MessageSent($message->fresh(['conversation', 'author'])));

        if ($conversation->client) {
            $this->dashboard->forget($conversation->client);
        }

        return $message;
    }
}
