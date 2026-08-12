<div class="zy-portal-page">
    <header class="zy-portal-page__header">
        <div class="zy-portal-page__intro">
            <p class="zy-section__eyebrow">Inbox</p>
            <h1 class="zy-portal-page__title">Messages</h1>
            <p class="zy-portal-page__lead">Talk directly with the Zytech team about your works.</p>
        </div>
    </header>

    <div class="zy-portal-split">
        <aside class="zy-portal-panel">
            <h2 class="zy-portal-panel__title">Conversations</h2>
            @forelse ($conversations as $conversation)
                <button type="button" class="zy-portal-list-btn @if ($active?->id === $conversation->id) is-active @endif" wire:click="select('{{ $conversation->id }}')">
                    <strong>{{ $conversation->subject }}</strong>
                    <span class="zy-muted">{{ $conversation->status->label() }}</span>
                </button>
            @empty
                <p class="zy-portal-empty">No conversations yet.</p>
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
                <p class="zy-portal-empty">Select or start a conversation.</p>
            @endif
        </section>
    </div>
</div>
