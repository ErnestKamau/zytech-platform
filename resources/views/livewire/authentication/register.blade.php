<div class="zy-auth-form zy-auth-form--register">
    <h1 class="zy-auth-form__title">Create an account</h1>
    <p class="zy-auth-form__lead">Join the Zytech client portal to follow your build.</p>

    <form wire:submit="register" method="POST" class="zy-stack">
        <div class="zy-field">
            <label class="zy-label" for="name">Full name</label>
            <div class="zy-field__control">
                <svg class="zy-icon zy-field__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
                <input id="name" type="text" class="zy-input zy-input--icon" wire:model="name" placeholder="Your name" required>
            </div>
            @error('name') <p class="zy-field-error">{{ $message }}</p> @enderror
        </div>

        <div class="zy-auth-form__pair">
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

            <div class="zy-field">
                <label class="zy-label" for="phone">Phone <span class="zy-muted">(optional)</span></label>
                <div class="zy-field__control">
                    <svg class="zy-icon zy-field__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                    </svg>
                    <input id="phone" type="tel" class="zy-input zy-input--icon" wire:model="phone" autocomplete="tel" placeholder="+254…">
                </div>
                @error('phone') <p class="zy-field-error">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="zy-auth-form__pair">
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
                        autocomplete="new-password"
                        placeholder="Min. 8 characters"
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

            <div class="zy-field">
                <label class="zy-label" for="password_confirmation">Confirm</label>
                <div class="zy-field__control">
                    <svg class="zy-icon zy-field__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                    <input id="password_confirmation" type="password" class="zy-input zy-input--icon" wire:model="password_confirmation" autocomplete="new-password" placeholder="Repeat password" required>
                </div>
            </div>
        </div>

        <button type="submit" class="zy-btn zy-btn--primary zy-btn--lg" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="register">Continue with email</span>
            <span wire:loading wire:target="register">Creating account…</span>
        </button>
    </form>

    <x-ui.auth-social context="register" />

    <p class="zy-auth-form__footer">
        Already have an account? <a href="{{ route('login') }}" class="zy-auth-form__link">Log in</a>
    </p>
</div>
