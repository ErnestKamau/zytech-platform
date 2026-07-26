<div class="zy-auth-form">
    <h1 class="zy-auth-form__title">Sign in</h1>
    <p class="zy-auth-form__lead">Access your Zytech account.</p>

    <form wire:submit="login" class="zy-stack">
        <div class="zy-field">
            <label class="zy-label" for="email">Email</label>
            <input id="email" type="email" class="zy-input" wire:model="email" autocomplete="username" required>
            @error('email') <p class="zy-field-error">{{ $message }}</p> @enderror
        </div>

        <div class="zy-field" x-data="{ show: false }">
            <label class="zy-label" for="password">Password</label>
            <div class="zy-input-group">
                <input id="password" :type="show ? 'text' : 'password'" class="zy-input" wire:model="password" autocomplete="current-password" required>
                <button type="button" class="zy-input-group__action" @click="show = !show" x-text="show ? 'Hide' : 'Show'"></button>
            </div>
            @error('password') <p class="zy-field-error">{{ $message }}</p> @enderror
        </div>

        <label class="zy-check">
            <input type="checkbox" wire:model="remember">
            <span>Remember me</span>
        </label>

        <button type="submit" class="zy-btn zy-btn--primary" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="login">Sign in</span>
            <span wire:loading wire:target="login">Signing in…</span>
        </button>
    </form>

    <p class="zy-auth-form__footer">
        <a href="{{ route('password.request') }}">Forgot password?</a>
        ·
        <a href="{{ route('register') }}">Create account</a>
    </p>
</div>
