<div class="zy-auth-form">
    <h1 class="zy-auth-form__title">Forgot password</h1>
    <p class="zy-auth-form__lead">We’ll email you a reset link.</p>

    @if ($status)
        <div class="zy-alert zy-alert--success" role="status">{{ $status }}</div>
    @endif

    <form wire:submit="sendResetLink" class="zy-stack">
        <div class="zy-field">
            <label class="zy-label" for="email">Email</label>
            <input id="email" type="email" class="zy-input" wire:model="email" required>
            @error('email') <p class="zy-field-error">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="zy-btn zy-btn--primary">Send reset link</button>
    </form>

    <p class="zy-auth-form__footer">
        <a href="{{ route('login') }}">Back to sign in</a>
    </p>
</div>
