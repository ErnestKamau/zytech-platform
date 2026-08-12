<div class="zy-portal-page">
    <x-portal.page-header
        eyebrow="Account"
        title="Security"
        lead="Password and two-factor authentication (email or SMS OTP)."
        icon="shield"
    />

    <div class="zy-portal-card">
        @if (session('status'))
            <p class="zy-alert zy-alert--success" role="status">{{ session('status') }}</p>
        @endif

        <form wire:submit="updatePassword" class="zy-stack">
            <div class="zy-portal-panel__title-wrap">
                <span class="zy-portal-panel__icon" aria-hidden="true"><x-portal.icon name="shield" /></span>
                <h2 class="zy-portal-card__subtitle">Change password</h2>
            </div>

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

        <div class="zy-stack">
            <h2 class="zy-portal-card__subtitle">Phone for SMS OTP</h2>
            <p class="zy-muted">International format required for Twilio, e.g. +254712345678.</p>
            <form wire:submit="savePhone" class="zy-stack">
                <div class="zy-field">
                    <label class="zy-label" for="phone">Phone</label>
                    <input id="phone" type="tel" class="zy-input" wire:model="phone" placeholder="+254712345678" autocomplete="tel">
                    @error('phone') <p class="zy-field-error">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="zy-btn zy-btn--secondary">Save phone</button>
            </form>
        </div>

        <div class="zy-stack">
            <h2 class="zy-portal-card__subtitle">Two-factor authentication</h2>
            <p class="zy-muted">
                @if ($mfa_enabled)
                    2FA is on. At sign-in you will choose email or SMS for a one-time code.
                @else
                    After your password, Zytech will ask for a one-time code by email and/or SMS.
                @endif
            </p>

            @if ($mfa_enabled)
                <button type="button" class="zy-btn zy-btn--danger" wire:click="disableTwoFactor">Disable 2FA</button>
            @else
                <label class="zy-checkbox">
                    <input type="checkbox" wire:model="mfa_email_enabled">
                    <span>Email OTP</span>
                </label>
                @error('mfa_email_enabled') <p class="zy-field-error">{{ $message }}</p> @enderror

                <label class="zy-checkbox">
                    <input type="checkbox" wire:model="mfa_sms_enabled">
                    <span>SMS OTP (Twilio)</span>
                </label>

                <div class="zy-field">
                    <span class="zy-label">Confirm with</span>
                    <div class="zy-stack" style="gap: var(--zy-space-2);">
                        <label class="zy-checkbox">
                            <input type="radio" wire:model="enrollment_channel" value="email">
                            <span>Email code</span>
                        </label>
                        <label class="zy-checkbox">
                            <input type="radio" wire:model="enrollment_channel" value="sms">
                            <span>SMS code</span>
                        </label>
                    </div>
                    @error('enrollment_channel') <p class="zy-field-error">{{ $message }}</p> @enderror
                </div>

                @if (! $awaitingEnrollmentCode)
                    <button type="button" class="zy-btn zy-btn--primary" wire:click="beginEnableTwoFactor" wire:loading.attr="disabled">
                        Send confirmation code
                    </button>
                @else
                    <div class="zy-field">
                        <label class="zy-label" for="enrollment_code">Confirmation code</label>
                        <input id="enrollment_code" type="text" inputmode="numeric" maxlength="6" class="zy-input" wire:model="enrollment_code" autocomplete="one-time-code">
                        @error('enrollment_code') <p class="zy-field-error">{{ $message }}</p> @enderror
                    </div>
                    <button type="button" class="zy-btn zy-btn--primary" wire:click="confirmEnableTwoFactor">Confirm and enable 2FA</button>
                    <button type="button" class="zy-btn zy-btn--ghost" wire:click="beginEnableTwoFactor">Resend code</button>
                @endif
            @endif
        </div>

        <div class="zy-stack">
            <h2 class="zy-portal-card__subtitle">Sign out</h2>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="zy-btn zy-btn--danger">Sign out</button>
            </form>
        </div>
    </div>
</div>
