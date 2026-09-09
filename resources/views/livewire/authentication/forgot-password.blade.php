<div class="zy-auth-form">
    <h1 class="zy-auth-form__title">Forgot password</h1>
    <p class="zy-auth-form__lead">Enter your email and we’ll send a reset link.</p>

    @if ($status)
        <div class="zy-alert zy-alert--success" role="status">{{ $status }}</div>
    @endif

    <form wire:submit="sendResetLink" class="zy-stack">
        <div class="zy-field">
            <label class="zy-label" for="email">Email</label>
            <div class="zy-field__control">
                <svg class="zy-icon zy-field__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                </svg>
                <input id="email" type="email" class="zy-input zy-input--icon" wire:model="email" placeholder="Email address" required>
            </div>
            @error('email') <p class="zy-field-error">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="zy-btn zy-btn--primary zy-btn--lg" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="sendResetLink">Send reset link</span>
            <span wire:loading wire:target="sendResetLink">Sending…</span>
        </button>
    </form>

    <p class="zy-auth-form__footer">
        <a href="{{ route('login') }}" class="zy-auth-form__link">Back to sign in</a>
    </p>
</div>
