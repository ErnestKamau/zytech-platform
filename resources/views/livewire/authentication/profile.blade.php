<div class="zy-portal-page">
    <x-portal.page-header
        eyebrow="Account"
        title="Profile"
        lead="Update your name and contact details used across the portal."
        icon="user"
    />

    <div class="zy-portal-card">
        <div class="zy-portal-panel__title-wrap" style="margin-bottom: var(--zy-space-4);">
            <span class="zy-portal-panel__icon" aria-hidden="true"><x-portal.icon name="user" /></span>
            <h2 class="zy-portal-card__subtitle">Your details</h2>
        </div>

        <form wire:submit="save" class="zy-stack">
            <div class="zy-field">
                <label class="zy-label" for="name">Full name</label>
                <input id="name" type="text" class="zy-input" wire:model="name" required>
                @error('name') <p class="zy-field-error">{{ $message }}</p> @enderror
            </div>

            <div class="zy-field">
                <label class="zy-label" for="email">Email</label>
                <input id="email" type="email" class="zy-input" wire:model="email" required>
                @error('email') <p class="zy-field-error">{{ $message }}</p> @enderror
            </div>

            <div class="zy-field">
                <label class="zy-label" for="phone">Phone</label>
                <input id="phone" type="text" class="zy-input" wire:model="phone">
                @error('phone') <p class="zy-field-error">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="zy-btn zy-btn--primary">Save profile</button>
        </form>
    </div>
</div>
