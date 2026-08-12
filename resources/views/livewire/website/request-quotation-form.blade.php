@php
    $images = config('zyntech-media.images');
    $hero = $images['commercial_courtyard'];
@endphp

<div>
    <div class="zy-container zy-quote-intro">
            <x-media.banner :src="asset($hero['path'])" :alt="$hero['alt']">
                <p class="zy-eyebrow" style="color: rgb(255 255 255 / 0.75);">Get a quote</p>
                <h1 style="color: #fff;">Request a quotation</h1>
                <p>Tell us about your project across Nairobi, Kiambu, or nationwide — we respond within one business day.</p>
            </x-media.banner>
        </div>

        <div class="zy-container zy-quote-form-wrap">
            <form wire:submit="submit" class="zy-quote-form">
                <div class="zy-quote-form__grid">
                    <div>
                        <label class="zy-label" for="quote-name">Full name</label>
                        <input id="quote-name" type="text" class="zy-input" wire:model="fullName" required>
                        @error('fullName') <p class="zy-form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="zy-label" for="quote-email">Email</label>
                        <input id="quote-email" type="email" class="zy-input" wire:model="email" required>
                        @error('email') <p class="zy-form-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="zy-label" for="quote-phone">Phone</label>
                        <input id="quote-phone" type="tel" class="zy-input" wire:model="phone" placeholder="+254 …">
                    </div>
                    <div>
                        <label class="zy-label" for="quote-contact">Preferred contact</label>
                        <select id="quote-contact" class="zy-input" wire:model="preferredContactMethod">
                            @foreach ($contactMethods as $method)
                                <option value="{{ $method->value }}">{{ $method->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="zy-label" for="quote-type">Project type</label>
                        <select id="quote-type" class="zy-input" wire:model="projectType">
                            @foreach ($projectTypes as $type)
                                <option value="{{ $type->value }}">{{ $type->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="zy-label" for="quote-county">County</label>
                        <input id="quote-county" type="text" class="zy-input" wire:model="county" required>
                    </div>
                    <div>
                        <label class="zy-label" for="quote-location">Project location</label>
                        <input id="quote-location" type="text" class="zy-input" wire:model="location" placeholder="Estate, road, or landmark">
                    </div>
                    <div>
                        <label class="zy-label" for="quote-budget">Budget range</label>
                        <select id="quote-budget" class="zy-input" wire:model="budgetRange">
                            @foreach ($budgetRanges as $range)
                                <option value="{{ $range->value }}">{{ $range->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="zy-label" for="quote-timeline">Estimated timeline</label>
                        <input id="quote-timeline" type="text" class="zy-input" wire:model="estimatedTimeline" placeholder="e.g. Start in 3 months">
                    </div>
                </div>

                <div>
                    <label class="zy-label" for="quote-description">Project description</label>
                    <textarea id="quote-description" class="zy-textarea" wire:model="description" rows="5" required placeholder="Scope, size, finishes, and any approvals already in place…"></textarea>
                    @error('description') <p class="zy-form-error">{{ $message }}</p> @enderror
                </div>

                @if ($services->isNotEmpty())
                    <fieldset class="zy-quote-services">
                        <legend class="zy-label">Required services</legend>
                        <div class="zy-quote-services__grid">
                            @foreach ($services as $service)
                                <label class="zy-quote-service">
                                    <input type="checkbox" wire:model="selectedServices" value="{{ $service->id }}">
                                    <span>{{ $service->title }}</span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                @endif

                <div>
                    <label class="zy-label" for="quote-files">Attachments (optional)</label>
                    <input id="quote-files" type="file" class="zy-input" wire:model="attachments" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.zip">
                    <p class="zy-form-hint">PDF, images, or drawings up to 10 MB each — max 5 files.</p>
                    @error('attachments.*') <p class="zy-form-error">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="zy-btn zy-btn--primary zy-btn--lg" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="submit">Submit request</span>
                    <span wire:loading wire:target="submit">Sending…</span>
                </button>
            </form>
        </div>
</div>
