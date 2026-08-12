@props([
    'eyebrow' => 'Next step',
    'headline' => 'Ready to build something that lasts?',
    'lead' => "Tell us about your project and get a detailed quotation from our estimation team within 48 hours.",
])

<section {{ $attributes->class('zy-section') }}>
    <div class="zy-container">
        <div class="zy-cta">
            <p class="zy-section__eyebrow">{{ $eyebrow }}</p>
            <h2>{{ $headline }}</h2>
            <p>{{ $lead }}</p>
            <div class="zy-cta__actions">
                {{ $slot }}
            </div>
        </div>
    </div>
</section>
