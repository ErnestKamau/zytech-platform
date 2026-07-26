<div class="zy-auth-form">
    <h1 class="zy-auth-form__title">Create account</h1>
    <p class="zy-auth-form__lead">Register as a Zytech client.</p>

    <form wire:submit="register" class="zy-stack">
        <div class="zy-field">
            <label class="zy-label" for="name">Full name</label>
            <input id="name" type="text" class="zy-input" wire:model="name" required>
            @error('name') <p class="zy-field-error">{{ $message }}</p> @enderror
        </div>

        <div class="zy-field">
            <label class="zy-label" for="email">Email</label>
            <input id="email" type="email" class="zy-input" wire:model="email" autocomplete="username" required>
            @error('email') <p class="zy-field-error">{{ $message }}</p> @enderror
        </div>

        <div class="zy-field">
            <label class="zy-label" for="password">Password</label>
            <input id="password" type="password" class="zy-input" wire:model="password" autocomplete="new-password" required>
            @error('password') <p class="zy-field-error">{{ $message }}</p> @enderror
        </div>

        <div class="zy-field">
            <label class="zy-label" for="password_confirmation">Confirm password</label>
            <input id="password_confirmation" type="password" class="zy-input" wire:model="password_confirmation" autocomplete="new-password" required>
        </div>

        <button type="submit" class="zy-btn zy-btn--primary" wire:loading.attr="disabled">
            Create account
        </button>
    </form>

    <p class="zy-auth-form__footer">
        Already registered? <a href="{{ route('login') }}">Sign in</a>
    </p>
</div>
