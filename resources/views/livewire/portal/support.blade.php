<div class="zy-portal-page">
    <header class="zy-portal-page__header">
        <h1 class="zy-portal-page__title">Support</h1>
    </header>

    <div class="zy-portal-split">
        <aside class="zy-portal-panel">
            <h2>Tickets</h2>
            @forelse ($tickets as $ticket)
                <button type="button" class="zy-portal-list-btn @if ($active?->id === $ticket->id) is-active @endif" wire:click="select('{{ $ticket->id }}')">
                    <strong>{{ $ticket->reference_number }}</strong>
                    <span class="zy-muted">{{ $ticket->subject }} · {{ $ticket->status->label() }}</span>
                </button>
            @empty
                <p class="zy-muted">No tickets yet.</p>
            @endforelse

            <form wire:submit="createTicket" class="zy-form zy-portal-form">
                <h3>New ticket</h3>
                <label>Subject<input type="text" wire:model="subject"></label>
                @error('subject') <p class="zy-alert zy-alert--danger">{{ $message }}</p> @enderror
                <label>Details<textarea wire:model="body" rows="3"></textarea></label>
                @error('body') <p class="zy-alert zy-alert--danger">{{ $message }}</p> @enderror
                <button type="submit" class="zy-btn zy-btn--primary zy-btn--sm">Open ticket</button>
            </form>
        </aside>

        <section class="zy-portal-panel">
            @if ($active)
                <h2>{{ $active->subject }}</h2>
                <p class="zy-muted">{{ $active->reference_number }} · {{ $active->status->label() }}</p>
                <div class="zy-portal-thread">
                    @foreach ($active->replies as $reply)
                        <article class="zy-portal-bubble">
                            <strong>{{ $reply->author?->name }} @if ($reply->is_staff)<span class="zy-badge">Staff</span>@endif</strong>
                            <p>{{ $reply->body }}</p>
                        </article>
                    @endforeach
                </div>
                <form wire:submit="sendReply" class="zy-form zy-portal-form">
                    <label>Reply<textarea wire:model="reply" rows="3"></textarea></label>
                    @error('reply') <p class="zy-alert zy-alert--danger">{{ $message }}</p> @enderror
                    <button type="submit" class="zy-btn zy-btn--primary zy-btn--sm">Reply</button>
                </form>
            @else
                <p class="zy-muted">Select or open a support ticket.</p>
            @endif
        </section>
    </div>
</div>
