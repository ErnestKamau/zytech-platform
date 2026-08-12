<div class="zy-portal-page">
    <x-portal.page-header
        eyebrow="Help"
        title="Support"
        lead="Open a ticket when you need account or project assistance."
        icon="ticket"
    />

    <div class="zy-portal-split zy-portal-split--elevated">
        <aside class="zy-portal-panel">
            <div class="zy-portal-panel__title-wrap">
                <span class="zy-portal-panel__icon" aria-hidden="true"><x-portal.icon name="ticket" /></span>
                <h2 class="zy-portal-panel__title">Tickets</h2>
            </div>
            @forelse ($tickets as $ticket)
                <button type="button" class="zy-portal-list-btn @if ($active?->id === $ticket->id) is-active @endif" wire:click="select('{{ $ticket->id }}')">
                    <strong>{{ $ticket->reference_number }}</strong>
                    <span class="zy-muted">{{ $ticket->subject }} · {{ $ticket->status->label() }}</span>
                </button>
            @empty
                <x-portal.empty-state icon="ticket" description="No tickets yet." />
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
                <x-portal.empty-state icon="ticket" description="Select or open a support ticket." />
            @endif
        </section>
    </div>
</div>
