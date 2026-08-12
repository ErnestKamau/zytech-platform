<div class="zy-portal-page">
    <x-portal.page-header
        eyebrow="Inbox"
        title="Messages"
        lead="Talk directly with the Zytech team about your works."
        icon="chat"
    />

    <div class="zy-portal-split zy-portal-split--elevated">
        <aside class="zy-portal-panel">
            <div class="zy-portal-panel__title-wrap">
                <span class="zy-portal-panel__icon" aria-hidden="true"><x-portal.icon name="chat" /></span>
                <h2 class="zy-portal-panel__title">Conversations</h2>
            </div>
            @forelse ($conversations as $conversation)
                <button type="button" class="zy-portal-list-btn @if ($active?->id === $conversation->id) is-active @endif" wire:click="select('{{ $conversation->id }}')">
                    <strong>{{ $conversation->subject }}</strong>
                    <span class="zy-muted">{{ $conversation->status->label() }}</span>
                </button>
            @empty
                <x-portal.empty-state icon="chat" description="No conversations yet." />
            @endforelse

            <form wire:submit="openConversation" class="zy-portal-form">
                <h3>Start conversation</h3>
                <div class="zy-field">
                    <label class="zy-label" for="portal-msg-subject">Subject</label>
                    <input id="portal-msg-subject" type="text" class="zy-input" wire:model="subject">
                    @error('subject') <p class="zy-field-error">{{ $message }}</p> @enderror
                </div>
                <div class="zy-field">
                    <label class="zy-label" for="portal-msg-body">Message</label>
                    <textarea id="portal-msg-body" class="zy-textarea" rows="3" wire:model="body"></textarea>
                    @error('body') <p class="zy-field-error">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="zy-btn zy-btn--primary zy-btn--sm">Send</button>
            </form>
        </aside>

        <section class="zy-portal-panel">
            @if ($active)
                <div class="zy-portal-panel__header">
                    <h2 class="zy-portal-panel__title">{{ $active->subject }}</h2>
                    <span class="zy-badge zy-badge--neutral">{{ $active->status->label() }}</span>
                </div>
                <div class="zy-portal-thread">
                    @foreach ($active->messages as $message)
                        <article class="zy-portal-bubble">
                            <strong>{{ $message->author?->name ?? 'Zytech' }}</strong>
                            <p>{{ $message->body }}</p>
                            <span class="zy-muted">{{ $message->created_at?->diffForHumans() }}</span>
                        </article>
                    @endforeach
                </div>
                <form wire:submit="sendReply" class="zy-portal-form">
                    <div class="zy-field">
                        <label class="zy-label" for="portal-msg-reply">Reply</label>
                        <textarea id="portal-msg-reply" class="zy-textarea" rows="3" wire:model="reply"></textarea>
                        @error('reply') <p class="zy-field-error">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" class="zy-btn zy-btn--primary zy-btn--sm">Reply</button>
                </form>
            @else
                <x-portal.empty-state icon="chat" description="Select or start a conversation." />
            @endif
        </section>
    </div>
</div>
