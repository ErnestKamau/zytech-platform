<div class="zy-portal-card">
    <h1 class="zy-portal-card__title">Security</h1>
    <p class="zy-portal-card__lead">Password and multi-factor preferences.</p>

    <form wire:submit="updatePassword" class="zy-stack">
        <h2 class="zy-portal-card__subtitle">Change password</h2>

        <div class="zy-field">
            <label class="zy-label" for="current_password">Current password</label>
            <input id="current_password" type="password" class="zy-input" wire:model="current_password" autocomplete="current-password">
            @error('current_password') <p class="zy-field-error">{{ $message }}</p> @enderror
        </div>

        <div class="zy-field">
            <label class="zy-label" for="password">New password</label>
            <input id="password" type="password" class="zy-input" wire:model="password" autocomplete="new-password">
            @error('password') <p class="zy-field-error">{{ $message }}</p> @enderror
        </div>

        <div class="zy-field">
            <label class="zy-label" for="password_confirmation">Confirm password</label>
            <input id="password_confirmation" type="password" class="zy-input" wire:model="password_confirmation" autocomplete="new-password">
        </div>

        <button type="submit" class="zy-btn zy-btn--primary">Update password</button>
    </form>

    <div class="zy-stack zy-stack--gap">
        <h2 class="zy-portal-card__subtitle">Multi-factor authentication</h2>
        <p>Foundation toggle only — authenticator setup arrives in a later release.</p>
        <button type="button" class="zy-btn zy-btn--secondary" wire:click="toggleMfaFoundation">
            {{ $mfa_enabled ? 'Disable MFA preference' : 'Enable MFA preference' }}
        </button>
    </div>

    <div class="zy-stack zy-stack--gap">
        <h2 class="zy-portal-card__subtitle">Sign out</h2>
        <button type="button" class="zy-btn zy-btn--danger" wire:click="logout">Sign out</button>
    </div>
</div>
