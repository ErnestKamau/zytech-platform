<div class="zy-auth-form">
    <h1 class="zy-auth-form__title">Welcome back</h1>
    <p class="zy-auth-form__lead">Sign in to track your Zytech projects.</p>

    <form wire:submit="login" method="POST" class="zy-stack">
        <div class="zy-field">
            <label class="zy-label" for="email">Email</label>
            <div class="zy-field__control">
                <svg class="zy-icon zy-field__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                </svg>
                <input id="email" type="email" class="zy-input zy-input--icon" wire:model="email" autocomplete="username" placeholder="Email address" required>
            </div>
            @error('email') <p class="zy-field-error">{{ $message }}</p> @enderror
        </div>

        <div class="zy-field" x-data="{ show: false }">
            <label class="zy-label" for="password">Password</label>
            <div class="zy-field__control">
                <svg class="zy-icon zy-field__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                </svg>
                <input
                    id="password"
                    :type="show ? 'text' : 'password'"
                    class="zy-input zy-input--icon zy-input--icon-trailing"
                    wire:model="password"
                    autocomplete="current-password"
                    placeholder="Password"
                    required
                >
                <button
                    type="button"
                    class="zy-field__trailing-action"
                    x-on:click="show = !show"
                    x-bind:aria-label="show ? 'Hide password' : 'Show password'"
                >
                    <svg x-show="!show" class="zy-icon zy-icon--sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <svg x-show="show" x-cloak class="zy-icon zy-icon--sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243" />
                    </svg>
                </button>
            </div>
            @error('password') <p class="zy-field-error">{{ $message }}</p> @enderror
        </div>

        <div class="zy-auth-form__row">
            <label class="zy-checkbox">
                <input type="checkbox" wire:model="remember">
                <span>Remember me</span>
            </label>
            <a href="{{ route('password.request') }}" class="zy-auth-form__link">Forgot password?</a>
        </div>

        <button type="submit" class="zy-btn zy-btn--primary zy-btn--lg" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="login">Continue with email</span>
            <span wire:loading wire:target="login">Signing in…</span>
        </button>
    </form>

    <x-ui.auth-social context="login" />

    <p class="zy-auth-form__footer">
        Don’t have an account? <a href="{{ route('register') }}" class="zy-auth-form__link">Sign up</a>
    </p>
</div>
