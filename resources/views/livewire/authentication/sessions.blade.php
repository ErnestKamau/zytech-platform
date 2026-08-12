<div class="zy-portal-card">
    <div class="zy-portal-card__header">
        <div>
            <p class="zy-section__eyebrow">Account</p>
            <h1 class="zy-portal-card__title">Sessions</h1>
            <p class="zy-portal-card__lead">Devices currently signed in to your account.</p>
        </div>
        <button type="button" class="zy-btn zy-btn--secondary zy-btn--sm" wire:click="revokeOthers">Revoke other sessions</button>
    </div>

    @error('sessions') <p class="zy-field-error">{{ $message }}</p> @enderror

    <ul class="zy-session-list">
        @forelse ($sessions as $session)
            <li class="zy-session-list__item">
                <div>
                    <strong>{{ $session->ip_address ?? 'Unknown IP' }}</strong>
                    <p class="zy-muted">{{ \Illuminate\Support\Str::limit($session->user_agent, 80) }}</p>
                    <p class="zy-muted">Last active {{ \Illuminate\Support\Carbon::createFromTimestamp($session->last_activity)->diffForHumans() }}</p>
                </div>
                <div>
                    @if ($session->id === $currentSessionId)
                        <span class="zy-badge zy-badge--primary">Current</span>
                    @else
                        <button type="button" class="zy-btn zy-btn--ghost zy-btn--sm" wire:click="revoke('{{ $session->id }}')">Revoke</button>
                    @endif
                </div>
            </li>
        @empty
            <li class="zy-portal-empty">No database sessions found for this account.</li>
        @endforelse
    </ul>
</div>
