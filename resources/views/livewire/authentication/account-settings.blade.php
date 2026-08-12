<div class="zy-portal-page">
    <x-portal.page-header
        eyebrow="Account"
        title="Settings"
        lead="Overview of your identity on the Zytech platform."
        icon="cog"
    />

    <div class="zy-portal-card">
        <div class="zy-portal-panel__title-wrap" style="margin-bottom: var(--zy-space-2);">
            <span class="zy-portal-panel__icon" aria-hidden="true"><x-portal.icon name="cog" /></span>
            <h2 class="zy-portal-card__subtitle">Account overview</h2>
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
