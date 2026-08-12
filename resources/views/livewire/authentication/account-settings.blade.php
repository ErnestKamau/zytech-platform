<div class="zy-portal-page">
    <x-portal.page-header
        eyebrow="Account"
        title="Settings"
        lead="Overview of your identity on the Zytech platform."
        icon="cog"
    />

    <div class="zy-account-card">
        <div class="zy-account-section">
            <div class="zy-portal-panel__title-wrap">
                <span class="zy-portal-panel__icon" aria-hidden="true"><x-portal.icon name="cog" /></span>
                <div>
                    <h2 class="zy-portal-card__subtitle">Account overview</h2>
                    <p class="zy-muted">Read-only summary of how this account is set up.</p>
                </div>
            </div>

            <div class="zy-account-quick-links">
                <a href="{{ route('account.profile') }}" class="zy-btn zy-btn--secondary zy-btn--sm">Edit profile</a>
                <a href="{{ route('account.security') }}" class="zy-btn zy-btn--ghost zy-btn--sm">Security</a>
                <a href="{{ route('account.sessions') }}" class="zy-btn zy-btn--ghost zy-btn--sm">Sessions</a>
            </div>

            <dl class="zy-definition-list">
                <div>
                    <dt>Name</dt>
                    <dd>{{ $user->name }}</dd>
                </div>
                <div>
                    <dt>Email</dt>
                    <dd>{{ $user->email }}</dd>
                </div>
                <div>
                    <dt>Type</dt>
                    <dd>{{ $user->type?->label() ?? '—' }}</dd>
                </div>
                <div>
                    <dt>Email verified</dt>
                    <dd>{{ $user->hasVerifiedEmail() ? 'Yes' : 'No' }}</dd>
                </div>
                <div>
                    <dt>Roles</dt>
                    <dd>{{ $user->getRoleNames()->join(', ') ?: 'None' }}</dd>
                </div>
                <div>
                    <dt>Account status</dt>
                    <dd>{{ $user->isLocked() ? 'Locked' : 'Active' }}</dd>
                </div>
            </dl>
        </div>
    </div>
</div>
