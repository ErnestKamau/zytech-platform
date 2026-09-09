@props([
    'headline' => 'Built on Kenyan soil, engineered to last.',
    'support' => 'Interior, exterior, and structural work across Nairobi, Kiambu, and nationwide — from first sketch to final handover.',
])

@php
    $images = config('zyntech-media.images');
    $heroKey = config('zyntech-media.homepage.hero', 'hero_modern_residence');
    $hero = $images[$heroKey] ?? $images['hero_modern_residence'] ?? $images['about_architecture'] ?? null;
@endphp

<section class="zy-hero">
    @if ($hero)
        <x-media.hero
            :image="asset($hero['path'])"
            :alt="$hero['alt']"
        />
    @endif

    <div class="zy-hero__inner">
        <div class="zy-hero__copy">
            <p class="zy-hero__eyebrow">Nairobi · Kiambu · Kenya</p>
            <h1 class="zy-hero__headline">{{ $headline }}</h1>
            <p class="zy-hero__support">{{ $support }}</p>
            <div class="zy-hero__actions">
                @isset($actions)
                    {{ $actions }}
                @else
                    <a href="{{ route('quote.index') }}" class="zy-btn zy-btn--inverse zy-btn--lg">
                        Request a Quote
                        <svg class="zy-icon zy-icon--sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                    <a href="{{ route('projects.index') }}" class="zy-btn zy-btn--frost zy-btn--lg">View Projects</a>
                @endisset
            </div>
        </div>
    </div>
</section>
