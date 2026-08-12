<?php

namespace App\Domains\Portal\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Portal\Actions\OpenConversation;
use App\Domains\Portal\Actions\SendMessage;
use App\Domains\Portal\Livewire\Concerns\ResolvesPortalClient;
use App\Domains\Portal\Repositories\MessageRepository;
use App\Domains\Portal\Services\MessageService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.portal')]
#[Title('Messages')]
final class Messages extends BaseComponent
{
    use ResolvesPortalClient;

    public ?string $activeId = null;

    public string $subject = '';

    public string $body = '';

    public string $reply = '';

    public function openConversation(OpenConversation $action): void
    {
        $this->validate([
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $user = Auth::user();
        abort_unless($user !== null, 403);

        $conversation = $action->handle($this->portalClient(), $this->subject, $this->body, $user);
        $this->reset(['subject', 'body']);
        $this->activeId = $conversation->id;
        session()->flash('status', 'Conversation started.');
    }

    public function select(string $id): void
    {
        $this->activeId = $id;
        $this->reply = '';
    }

    public function sendReply(SendMessage $action, MessageRepository $messages): void
    {
        abort_unless($this->activeId !== null, 404);

        $this->validate(['reply' => ['required', 'string', 'max:5000']]);

        $conversation = $messages->findForClient($this->portalClient(), $this->activeId);
        abort_unless($conversation !== null, 404);

        $user = Auth::user();
        abort_unless($user !== null, 403);

        $action->handle($conversation, $user, $this->reply);
        $this->reply = '';
        session()->flash('status', 'Message sent.');
    }

    public function render(MessageService $messages): View
    {
        $client = $this->portalClient();
        $conversations = $messages->conversations($client);
        $active = $this->activeId
            ? $conversations->firstWhere('id', $this->activeId)
            : $conversations->first();

        return view('livewire.portal.messages', [
            'conversations' => $conversations,
            'active' => $active,
        ]);
    }
}
