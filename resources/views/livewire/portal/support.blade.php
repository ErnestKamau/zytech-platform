<div class="zy-portal-page">
    <header class="zy-portal-page__header">
        <div class="zy-portal-page__intro">
            <p class="zy-section__eyebrow">Help</p>
            <h1 class="zy-portal-page__title">Support</h1>
            <p class="zy-portal-page__lead">Open a ticket when you need account or project assistance.</p>
        </div>
    </header>

    <div class="zy-portal-split">
        <aside class="zy-portal-panel">
            <h2 class="zy-portal-panel__title">Tickets</h2>
            @forelse ($tickets as $ticket)
                <button type="button" class="zy-portal-list-btn @if ($active?->id === $ticket->id) is-active @endif" wire:click="select('{{ $ticket->id }}')">
                    <strong>{{ $ticket->reference_number }}</strong>
                    <span class="zy-muted">{{ $ticket->subject }} · {{ $ticket->status->label() }}</span>
                </button>
            @empty
                <p class="zy-portal-empty">No tickets yet.</p>
            @endforelse

            <form wire:submit="createTicket" class="zy-portal-form">
                <h3>New ticket</h3>
                <div class="zy-field">
                    <label class="zy-label" for="portal-ticket-subject">Subject</label>
                    <input id="portal-ticket-subject" type="text" class="zy-input" wire:model="subject">
                    @error('subject') <p class="zy-field-error">{{ $message }}</p> @enderror
                </div>
                <div class="zy-field">
                    <label class="zy-label" for="portal-ticket-body">Details</label>
                    <textarea id="portal-ticket-body" class="zy-textarea" rows="3" wire:model="body"></textarea>
                    @error('body') <p class="zy-field-error">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="zy-btn zy-btn--primary zy-btn--sm">Open ticket</button>
            </form>
        </aside>

        <section class="zy-portal-panel">
            @if ($active)
                <div class="zy-portal-panel__header">
                    <h2 class="zy-portal-panel__title">{{ $active->subject }}</h2>
                    <span class="zy-badge zy-badge--primary">{{ $active->status->label() }}</span>
                </div>
                <p class="zy-muted">{{ $active->reference_number }}</p>
                <div class="zy-portal-thread">
                    @foreach ($active->replies as $reply)
                        <article class="zy-portal-bubble">
                            <strong>
                                {{ $reply->author?->name }}
                                @if ($reply->is_staff)
                                    <span class="zy-badge zy-badge--primary">Staff</span>
                                @endif
                            </strong>
                            <p>{{ $reply->body }}</p>
                        </article>
                    @endforeach
                </div>
                <form wire:submit="sendReply" class="zy-portal-form">
                    <div class="zy-field">
                        <label class="zy-label" for="portal-ticket-reply">Reply</label>
                        <textarea id="portal-ticket-reply" class="zy-textarea" rows="3" wire:model="reply"></textarea>
                        @error('reply') <p class="zy-field-error">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="zy-btn zy-btn--primary zy-btn--sm">Reply</button>
                </form>
            @else
                <p class="zy-portal-empty">Select or open a support ticket.</p>
            @endif
        </section>
    </div>
</div>
