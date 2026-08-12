<div class="zy-portal-page">
    <x-portal.page-header
        eyebrow="Account"
        title="Sessions"
        lead="Devices currently signed in to your account."
        icon="sessions"
    >
        <button type="button" class="zy-btn zy-btn--secondary zy-btn--sm" wire:click="revokeOthers">Revoke other sessions</button>
    </x-portal.page-header>

    <div class="zy-account-card">
        <div class="zy-account-section">
            <div class="zy-portal-panel__title-wrap">
                <span class="zy-portal-panel__icon" aria-hidden="true"><x-portal.icon name="sessions" /></span>
                <div>
                    <h2 class="zy-portal-card__subtitle">Active devices</h2>
                    <p class="zy-muted">Revoke anything you do not recognise.</p>
                </div>
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
                    <li>
                        <x-portal.empty-state icon="sessions" description="No database sessions found for this account." />
                    </li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
