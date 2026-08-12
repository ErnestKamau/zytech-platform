<?php

namespace App\Domains\Portal\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Domains\Portal\Actions\CreateSupportTicket;
use App\Domains\Portal\Actions\ReplyToTicket;
use App\Domains\Portal\Livewire\Concerns\ResolvesPortalClient;
use App\Domains\Portal\Repositories\SupportRepository;
use App\Domains\Portal\Services\SupportService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.portal')]
#[Title('Support')]
final class Support extends BaseComponent
{
    use ResolvesPortalClient;

    public ?string $activeId = null;

    public string $subject = '';

    public string $body = '';

    public string $reply = '';

    public function createTicket(CreateSupportTicket $action): void
    {
        $this->validate([
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $user = Auth::user();
        abort_unless($user !== null, 403);

        $ticket = $action->handle($this->portalClient(), $user, $this->subject, $this->body);
        $this->reset(['subject', 'body']);
        $this->activeId = $ticket->id;
        session()->flash('status', 'Support ticket opened.');
    }

    public function select(string $id): void
    {
        $this->activeId = $id;
        $this->reply = '';
    }

    public function sendReply(ReplyToTicket $action, SupportRepository $tickets): void
    {
        abort_unless($this->activeId !== null, 404);
        $this->validate(['reply' => ['required', 'string', 'max:5000']]);

        $ticket = $tickets->findForClient($this->portalClient(), $this->activeId);
        abort_unless($ticket !== null, 404);

        $user = Auth::user();
        abort_unless($user !== null, 403);

        $action->handle($ticket, $user, $this->reply, false);
        $this->reply = '';
        session()->flash('status', 'Reply sent.');
    }

    public function render(SupportService $support): View
    {
        $tickets = $support->forClient($this->portalClient());
        $active = $this->activeId
            ? $tickets->firstWhere('id', $this->activeId)
            : $tickets->first();

        return view('livewire.portal.support', [
            'tickets' => $tickets,
            'active' => $active,
        ]);
    }
}
