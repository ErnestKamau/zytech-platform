<div class="zy-auth-form">
    <h1 class="zy-auth-form__title">Verify your account</h1>
    <p class="zy-auth-form__lead">
        Confirm your account with a one-time code. That method stays on for every future sign-in.
    </p>

    @if (session('status'))
        <div class="zy-alert zy-alert--success" role="status">{{ session('status') }}</div>
    @endif

    <div class="zy-stack">
        <div class="zy-field">
            <span class="zy-label">Send code via</span>
            <div class="zy-stack" style="gap: var(--zy-space-2);">
                <label class="zy-checkbox">
                    <input type="radio" wire:model.live="channel" value="email">
                    <span>
                        Email
                        @if ($maskedEmail)
                            <span class="zy-muted">({{ $maskedEmail }})</span>
                        @endif
                    </span>
                </label>
                <label class="zy-checkbox">
                    <input type="radio" wire:model.live="channel" value="sms">
                    <span>
                        SMS / text
                        @if ($maskedPhone)
                            <span class="zy-muted">({{ $maskedPhone }})</span>
                        @endif
                    </span>
                </label>
            </div>
            @error('channel') <p class="zy-field-error">{{ $message }}</p> @enderror
        </div>

        @if ($needsPhone)
            <div class="zy-field">
                <label class="zy-label" for="phone">Phone number <span class="zy-muted">(required for SMS)</span></label>
                <input
                    id="phone"
                    type="tel"
                    class="zy-input"
                    wire:model="phone"
                    placeholder="+254712345678"
                    autocomplete="tel"
                    required
                >
                <p class="zy-field__message">International format required for Twilio, e.g. +254712345678.</p>
                @error('phone') <p class="zy-field-error">{{ $message }}</p> @enderror
            </div>
        @endif

        @if (! $codeSent)
            <button type="button" class="zy-btn zy-btn--primary zy-btn--lg" wire:click="sendCode" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="sendCode">Send verification code</span>
                <span wire:loading wire:target="sendCode">Sending…</span>
            </button>
        @else
            <form wire:submit="verify" class="zy-stack">
                <div class="zy-field">
                    <label class="zy-label" for="code">Verification code</label>
                    <input
                        id="code"
                        type="text"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        maxlength="6"
                        class="zy-input"
                        wire:model="code"
                        autocomplete="one-time-code"
                        placeholder="6-digit code"
                        required
                    >
                    @error('code') <p class="zy-field-error">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="zy-btn zy-btn--primary zy-btn--lg" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="verify">Verify and continue</span>
                    <span wire:loading wire:target="verify">Verifying…</span>
                </button>
            </form>

            <button type="button" class="zy-btn zy-btn--ghost" wire:click="sendCode" wire:loading.attr="disabled">
                Resend code
            </button>
        @endif

        <form method="POST" action="{{ route('logout') }}" class="zy-auth-form__footer">
            @csrf
            <button type="submit" class="zy-auth-form__link" style="background: none; border: 0; padding: 0; cursor: pointer; font: inherit;">Sign out</button>
        </form>
    </div>
</div>
