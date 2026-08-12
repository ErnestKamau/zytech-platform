<div class="zy-portal-card">
    <p class="zy-section__eyebrow">Account</p>
    <h1 class="zy-portal-card__title">Profile</h1>
    <p class="zy-portal-card__lead">Update your name and contact details used across the portal.</p>

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
