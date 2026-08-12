@props([
    'brand' => 'Zytech Contractors',
    'headline' => 'Built on Kenyan soil, engineered to last.',
    'support' => 'Interior, exterior, and structural work across Nairobi, Kiambu, and nationwide — from first sketch to final handover.',
])

@php
    $hero = config('zyntech-media.videos.hero_site_work');
@endphp

<section class="zy-hero">
    <x-media.hero
        :video="asset($hero['path'])"
        :poster="asset($hero['poster'])"
        :alt="$hero['alt']"
    />
    <div class="zy-container zy-hero__content">
        <p class="zy-hero__eyebrow">Nairobi · Kiambu · Kenya</p>
        <h1 class="zy-hero__brand">{{ $brand }}</h1>
        <p class="zy-hero__headline">{{ $headline }}</p>
        <p class="zy-hero__support">{{ $support }}</p>
        <div class="zy-hero__actions">
            @isset($actions)
                {{ $actions }}
            @else
                <a href="#quote" class="zy-btn zy-btn--gradient zy-btn--lg">Request a Quote</a>
                <a href="{{ route('projects.index') }}" class="zy-btn zy-btn--frost zy-btn--lg">View Projects</a>
            @endisset
        </div>
    </div>
    <p class="zy-hero__scroll-cue" aria-hidden="true">Scroll</p>
</section>
