<div class="zy-portal-card">
    <h1 class="zy-portal-card__title">Account</h1>
    <p class="zy-portal-card__lead">Overview of your identity on the Zytech platform.</p>

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
