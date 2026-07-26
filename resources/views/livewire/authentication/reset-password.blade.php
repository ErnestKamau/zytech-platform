<div class="zy-auth-form">
    <h1 class="zy-auth-form__title">Reset password</h1>
    <p class="zy-auth-form__lead">Choose a new password for your account.</p>

    <form wire:submit="resetPassword" class="zy-stack">
        <div class="zy-field">
            <label class="zy-label" for="email">Email</label>
            <input id="email" type="email" class="zy-input" wire:model="email" required>
            @error('email') <p class="zy-field-error">{{ $message }}</p> @enderror
        </div>

        <div class="zy-field">
            <label class="zy-label" for="password">New password</label>
            <input id="password" type="password" class="zy-input" wire:model="password" autocomplete="new-password" required>
            @error('password') <p class="zy-field-error">{{ $message }}</p> @enderror
        </div>

        <div class="zy-field">
            <label class="zy-label" for="password_confirmation">Confirm password</label>
            <input id="password_confirmation" type="password" class="zy-input" wire:model="password_confirmation" autocomplete="new-password" required>
        </div>

        <button type="submit" class="zy-btn zy-btn--primary">Reset password</button>
    </form>
</div>
