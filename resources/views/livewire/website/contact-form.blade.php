<div>
    @if ($sent)
        <div class="zy-alert zy-alert--success" role="status">Thanks — we’ll get back to you within 48 hours.</div>
    @endif

    <form wire:submit="submit" class="zy-contact-form">
        <div class="zy-field">
            <label class="zy-label" for="contact-name">Name</label>
            <input id="contact-name" type="text" class="zy-input" wire:model="name" placeholder="Your name" required>
            @error('name') <p class="zy-field-error">{{ $message }}</p> @enderror
        </div>

        <div class="zy-field">
            <label class="zy-label" for="contact-email">Email</label>
            <input id="contact-email" type="email" class="zy-input" wire:model="email" placeholder="you@company.co.ke" required>
            @error('email') <p class="zy-field-error">{{ $message }}</p> @enderror
        </div>

        <div class="zy-field">
            <label class="zy-label" for="contact-message">Message</label>
            <textarea id="contact-message" class="zy-textarea" wire:model="message" placeholder="Tell us about your project" required></textarea>
            @error('message') <p class="zy-field-error">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="zy-btn zy-btn--primary zy-btn--lg" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="submit">Submit</span>
            <span wire:loading wire:target="submit">Sending…</span>
        </button>
    </form>
</div>
