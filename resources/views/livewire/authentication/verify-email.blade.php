<div class="zy-auth-form">
    <div class="zy-auth-form__icon" aria-hidden="true">
        <svg class="zy-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    </div>
    <h1 class="zy-auth-form__title">Verify your account</h1>
    <p class="zy-auth-form__lead">
        We send the same one-time code to your email and phone. That stays on for every future sign-in.
    </p>

    @if (session('status'))
        <div class="zy-alert zy-alert--success" role="status">{{ session('status') }}</div>
    @endif

    <div class="zy-stack">
        <p class="zy-muted" style="margin: 0;">
            Email
            @if ($maskedEmail)
                <span>({{ $maskedEmail }})</span>
            @endif
            @if ($maskedPhone)
                · SMS ({{ $maskedPhone }})
            @endif
        </p>

        @if ($needsPhone)
            <div class="zy-field">
                <label class="zy-label" for="phone">Phone number</label>
                <div class="zy-field__control">
                    <svg class="zy-icon zy-field__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                    </svg>
                    <input
                        id="phone"
                        type="tel"
                        class="zy-input zy-input--icon"
                        wire:model="phone"
                        placeholder="+254712345678"
                        autocomplete="tel"
                        required
                    >
                </div>
                <p class="zy-field__message">International format required for Twilio, e.g. +254712345678.</p>
                @error('phone') <p class="zy-field-error">{{ $message }}</p> @enderror
            </div>
        @endif

        @if (! $codeSent)
            <button type="button" class="zy-btn zy-btn--primary zy-btn--lg" wire:click="sendCode" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="sendCode">Send verification code</span>
                <span wire:loading wire:target="sendCode">Sending…</span>
                <svg wire:loading.remove wire:target="sendCode" class="zy-icon zy-icon--sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                </svg>
            </button>
        @else
            <form wire:submit="verify" class="zy-stack">
                <div class="zy-field">
                    <label class="zy-label" for="code">Verification code</label>
                    <div class="zy-field__control">
                        <svg class="zy-icon zy-field__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                        <input
                            id="code"
                            type="text"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            maxlength="6"
                            class="zy-input zy-input--icon"
                            wire:model="code"
                            autocomplete="one-time-code"
                            placeholder="6-digit code"
                            required
                        >
                    </div>
                    @error('code') <p class="zy-field-error">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="zy-btn zy-btn--primary zy-btn--lg" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="verify">Verify and continue</span>
                    <span wire:loading wire:target="verify">Verifying…</span>
                    <svg wire:loading.remove wire:target="verify" class="zy-icon zy-icon--sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                    </svg>
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
