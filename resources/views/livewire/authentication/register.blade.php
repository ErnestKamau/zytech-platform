<div class="zy-auth-form">
    <h1 class="zy-auth-form__title">Create your account</h1>
    <p class="zy-auth-form__lead">Enter your details to join the Zytech client portal.</p>

    <form wire:submit="register" class="zy-stack">
        <div class="zy-field">
            <label class="zy-label" for="name">Full name</label>
            <input id="name" type="text" class="zy-input" wire:model="name" placeholder="eg. Jane Wanjiru" required>
            @error('name') <p class="zy-field-error">{{ $message }}</p> @enderror
        </div>

        <div class="zy-field">
            <label class="zy-label" for="email">Email</label>
            <input id="email" type="email" class="zy-input" wire:model="email" autocomplete="username" placeholder="eg. jane@company.co.ke" required>
            @error('email') <p class="zy-field-error">{{ $message }}</p> @enderror
        </div>

        <div class="zy-field" x-data="{ show: false }">
            <label class="zy-label" for="password">Password</label>
            <div class="zy-field__control">
                <input
                    id="password"
                    :type="show ? 'text' : 'password'"
                    class="zy-input zy-input--icon-trailing"
                    wire:model="password"
                    autocomplete="new-password"
                    placeholder="Create a password"
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
            <p class="zy-field__message">Must be at least 8 characters.</p>
            @error('password') <p class="zy-field-error">{{ $message }}</p> @enderror
        </div>

        <div class="zy-field">
            <label class="zy-label" for="password_confirmation">Confirm password</label>
            <input id="password_confirmation" type="password" class="zy-input" wire:model="password_confirmation" autocomplete="new-password" required>
        </div>

        <button type="submit" class="zy-btn zy-btn--primary zy-btn--lg" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="register">Create account</span>
            <span wire:loading wire:target="register">Creating account…</span>
        </button>
    </form>

    <p class="zy-auth-form__footer">
        Already have an account? <a href="{{ route('login') }}" class="zy-auth-form__link">Log in</a>
    </p>
</div>
