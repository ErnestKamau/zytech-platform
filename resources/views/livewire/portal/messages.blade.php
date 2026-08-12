<div class="zy-portal-page">
    <header class="zy-portal-page__header">
        <h1 class="zy-portal-page__title">Messages</h1>
    </header>

    <div class="zy-portal-split">
        <aside class="zy-portal-panel">
            <h2>Conversations</h2>
            @forelse ($conversations as $conversation)
                <button type="button" class="zy-portal-list-btn @if ($active?->id === $conversation->id) is-active @endif" wire:click="select('{{ $conversation->id }}')">
                    <strong>{{ $conversation->subject }}</strong>
                    <span class="zy-muted">{{ $conversation->status->label() }}</span>
                </button>
            @empty
                <p class="zy-muted">No conversations yet.</p>
            @endforelse

            <form wire:submit="openConversation" class="zy-form zy-portal-form">
                <h3>Start conversation</h3>
                <label>Subject<input type="text" wire:model="subject"></label>
                @error('subject') <p class="zy-alert zy-alert--danger">{{ $message }}</p> @enderror
                <label>Message<textarea wire:model="body" rows="3"></textarea></label>
                @error('body') <p class="zy-alert zy-alert--danger">{{ $message }}</p> @enderror
                <button type="submit" class="zy-btn zy-btn--primary zy-btn--sm">Send</button>
            </form>
        </aside>

        <section class="zy-portal-panel">
            @if ($active)
                <h2>{{ $active->subject }}</h2>
                <div class="zy-portal-thread">
                    @foreach ($active->messages as $message)
                        <article class="zy-portal-bubble">
                            <strong>{{ $message->author?->name ?? 'Zytech' }}</strong>
                            <p>{{ $message->body }}</p>
                            <span class="zy-muted">{{ $message->created_at?->diffForHumans() }}</span>
                        </article>
                    @endforeach
                </div>
                <form wire:submit="sendReply" class="zy-form zy-portal-form">
                    <label>Reply<textarea wire:model="reply" rows="3"></textarea></label>
                    @error('reply') <p class="zy-alert zy-alert--danger">{{ $message }}</p> @enderror
                    <button type="submit" class="zy-btn zy-btn--primary zy-btn--sm">Reply</button>
                </form>
            @else
                <p class="zy-muted">Select or start a conversation.</p>
            @endif
        </section>
    </div>
</div>
