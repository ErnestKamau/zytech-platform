<div class="zy-portal-page">
    <x-portal.page-header
        eyebrow="Account"
        title="Profile"
        lead="Update your photo, name, and contact details used across the portal."
        icon="user"
    />

    <div class="zy-account-card">
        <div class="zy-account-section">
            <div class="zy-portal-panel__title-wrap">
                <span class="zy-portal-panel__icon" aria-hidden="true"><x-portal.icon name="user" /></span>
                <h2 class="zy-portal-card__subtitle">Profile photo</h2>
            </div>

            <div class="zy-account-avatar-row">
                @if ($avatar)
                    <img src="{{ $avatar->temporaryUrl() }}" alt="" class="zy-portal__avatar zy-portal__avatar--lg zy-portal__avatar--image">
                @elseif ($avatarUrl)
                    <img src="{{ $avatarUrl }}" alt="" class="zy-portal__avatar zy-portal__avatar--lg zy-portal__avatar--image">
                @else
                    <span class="zy-portal__avatar zy-portal__avatar--lg" aria-hidden="true">{{ $initials }}</span>
                @endif

                <div>
                    <div class="zy-account-avatar-actions">
                        <label class="zy-btn zy-btn--primary zy-btn--sm" style="cursor: pointer;">
                            <x-portal.icon name="upload" />
                            Upload image
                            <input type="file" class="zy-sr-only" wire:model="avatar" accept="image/png,image/jpeg,image/gif,image/webp">
                        </label>
                        @if ($avatarUrl || $avatar)
                            <button type="button" class="zy-btn zy-btn--ghost zy-btn--sm" wire:click="removeAvatar" wire:confirm="Remove your profile photo?">
                                Remove
                            </button>
                        @endif
                    </div>
                    <p class="zy-account-avatar-hint">We support PNG, JPEG, GIF, and WebP under 5MB.</p>
                    @error('avatar') <p class="zy-field-error">{{ $message }}</p> @enderror
                    <div wire:loading wire:target="avatar" class="zy-muted" style="margin-top: 0.5rem;">Uploading…</div>
                </div>
            </div>
        </div>

        <form wire:submit="save" class="zy-account-section">
            <div class="zy-portal-panel__title-wrap">
                <span class="zy-portal-panel__icon" aria-hidden="true"><x-portal.icon name="user" /></span>
                <h2 class="zy-portal-card__subtitle">Personal information</h2>
            </div>

            <div class="zy-field">
                <label class="zy-label" for="name">Full name</label>
                <input id="name" type="text" class="zy-input" wire:model="name" required>
                @error('name') <p class="zy-field-error">{{ $message }}</p> @enderror
            </div>

            <div class="zy-field">
                <label class="zy-label" for="email">Email</label>
                <input id="email" type="email" class="zy-input" wire:model="email" required>
                <p class="zy-account-avatar-hint">Used to sign in to your account.</p>
                @error('email') <p class="zy-field-error">{{ $message }}</p> @enderror
            </div>

            <div class="zy-field">
                <label class="zy-label" for="phone">Phone</label>
                <input id="phone" type="text" class="zy-input" wire:model="phone" placeholder="+254…">
                @error('phone') <p class="zy-field-error">{{ $message }}</p> @enderror
            </div>

            <div class="zy-account-actions">
                <button type="button" class="zy-btn zy-btn--ghost" wire:click="cancel">Cancel</button>
                <button type="submit" class="zy-btn zy-btn--primary">Save profile</button>
            </div>
        </form>
    </div>
</div>
