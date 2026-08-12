<div class="zy-auth-form">
    <h1 class="zy-auth-form__title">Forgot password</h1>
    <p class="zy-auth-form__lead">Enter your email and we'll send you a reset link.</p>

    @if ($status)
        <div class="zy-alert zy-alert--success" role="status" style="margin-top: var(--zy-space-6);">{{ $status }}</div>
    @endif

    <form wire:submit="sendResetLink" class="zy-stack">
        <div class="zy-field">
            <label class="zy-label" for="email">Email</label>
            <input id="email" type="email" class="zy-input" wire:model="email" placeholder="eg. jane@company.co.ke" required>
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
