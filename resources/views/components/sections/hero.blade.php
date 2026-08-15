@props([
    'headline' => 'Built on Kenyan soil, engineered to last.',
    'support' => 'Interior, exterior, and structural work across Nairobi, Kiambu, and nationwide — from first sketch to final handover.',
])

@php
    $images = config('zyntech-media.images');
    $heroKey = config('zyntech-media.homepage.hero', 'hero_facade_dusk');
    $hero = $images[$heroKey] ?? $images['hero_facade_dusk'] ?? null;
@endphp

<section class="zy-hero">
    @if ($hero)
        <x-media.hero
            :image="asset($hero['path'])"
            :alt="$hero['alt']"
        />
    @endif
    <div class="zy-container zy-hero__content">
        <div class="zy-hero__lead">
            <p class="zy-hero__eyebrow">Nairobi · Kiambu · Kenya</p>
            <h1 class="zy-hero__headline">{{ $headline }}</h1>
        </div>
        <div class="zy-hero__aside">
            <p class="zy-hero__support">{{ $support }}</p>
            <div class="zy-hero__actions">
                @isset($actions)
                    {{ $actions }}
                @else
                    <a href="{{ route('quote.index') }}" class="zy-btn zy-btn--inverse zy-btn--lg">Request a Quote</a>
                    <a href="{{ route('projects.index') }}" class="zy-btn zy-btn--frost zy-btn--lg">View Projects</a>
                @endisset
            </div>
        </div>
    </div>
</section>
