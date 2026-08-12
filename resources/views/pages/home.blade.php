@extends('layouts.website')

@php
    $profile = $companyProfile ?? null;
    $stats = ($companyStatistics ?? collect())->map(fn ($stat) => [
        'value' => $stat->value,
        'label' => $stat->label,
    ]);
    $heroHeadline = filled($profile?->tagline)
        ? $profile->tagline.', engineered to last.'
        : 'Built on Kenyan soil, engineered to last.';
    $heroSupport = $profile?->shortDescription
        ?: 'Interior, exterior, and structural work across Nairobi, Kiambu, and nationwide — from first sketch to final handover.';

    $services = ($publishedServices ?? collect())->isNotEmpty()
        ? $publishedServices
        : collect(config('zyntech-services'));
    $featuredProjects = ($featuredProjects ?? collect())->isNotEmpty()
        ? $featuredProjects
        : collect();
    $featuredArticles = ($featuredArticles ?? collect())->isNotEmpty()
        ? $featuredArticles
        : collect();
    $images = config('zyntech-media.images');
    $walkway = $images['structural_walkway'];
    $courtyard = $images['commercial_courtyard'];
    $ballast = $images['site_prep_ballast'];
@endphp

@section('title', ($profile->name ?? 'Zytech Contractors').' — '.($profile->tagline ?? 'Built on Kenyan soil'))
@section('page-class', 'zy-page-home')

@section('content')
    <x-sections.hero
        :brand="$profile->name ?? 'Zytech Contractors'"
        :headline="$heroHeadline"
        :support="$heroSupport"
    />

    <section class="zy-story">
        <div class="zy-container">
            <x-media.gallery
                :left-src="asset($walkway['path'])"
                :left-alt="$walkway['alt']"
                :right-src="asset($courtyard['path'])"
                :right-alt="$courtyard['alt']"
            >
                <p class="zy-section__eyebrow">On the ground</p>
                <h2>If you can plan it, we can build it.</h2>
                <p>From ballast delivery to finished courtyards — real Kenyan sites, one accountable crew.</p>
                <a href="{{ route('services.index') }}" class="zy-btn zy-btn--primary">Explore services</a>
            </x-media.gallery>
        </div>
    </section>

    @if ($stats->isNotEmpty())
    <section class="zy-section zy-section--alt" style="padding-block: var(--zy-space-12);">
        <div class="zy-container">
            <div class="zy-stats">
                @foreach ($stats as $stat)
                    <div class="zy-stat">
                        <p class="zy-stat__value" style="font-size: var(--zy-text-3xl);">{{ $stat['value'] }}</p>
                        <p class="zy-stat__label">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <section class="zy-section">
        <div class="zy-container">
            <div class="zy-section__header">
                <p class="zy-section__eyebrow">What we do</p>
                <h2>Every discipline under one roof</h2>
                <p>Design, estimate, approve, and build with one accountable team — no hand-offs, no finger-pointing.</p>
            </div>

            <div class="zy-grid zy-grid--3">
                @foreach ($services as $service)
                    @if ($service instanceof \App\Domains\Service\Data\ServiceData)
                        <x-services.card :service="$service" />
                    @else
                        <x-ui.card interactive>
                            @if ($service['image'] && isset($images[$service['image']]))
                                <x-media.cover
                                    :src="asset($images[$service['image']]['path'])"
                                    :alt="$images[$service['image']]['alt']"
                                />
                            @else
                                <span class="zy-icon-tile {{ $service['featured'] ? 'zy-icon-tile--gradient' : '' }}" aria-hidden="true">
                                    <svg class="zy-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $service['icon'] }}" />
                                    </svg>
                                </span>
                            @endif
                            <p class="zy-card__title" style="margin-top: var(--zy-space-2);">{{ $service['title'] }}</p>
                            <p class="zy-card__body">{{ $service['body'] }}</p>
                        </x-ui.card>
                    @endif
                @endforeach
            </div>
        </div>
    </section>

    <section class="zy-section zy-section--alt">
        <div class="zy-container">
            <div class="zy-section__header">
                <p class="zy-section__eyebrow">Featured work</p>
                <h2>Projects that speak for themselves</h2>
                <p>Active and completed work across Nairobi and Kiambu — photographed on our own sites.</p>
            </div>

            <div class="zy-grid zy-grid--3">
                @if ($featuredProjects->isNotEmpty())
                    @foreach ($featuredProjects->take(3) as $project)
                        <x-projects.card :project="$project" />
                    @endforeach
                @else
                    <x-ui.card interactive>
                        <x-media.cover :src="asset($courtyard['path'])" :alt="$courtyard['alt']" />
                        <p class="zy-card__eyebrow">Commercial</p>
                        <p class="zy-card__title">Commercial Courtyard — Stone &amp; Paving</p>
                        <p class="zy-card__body">Completed · Nairobi</p>
                    </x-ui.card>

                    <x-ui.card interactive>
                        <x-media.cover :src="asset($ballast['path'])" :alt="$ballast['alt']" />
                        <p class="zy-card__eyebrow">Site preparation</p>
                        <p class="zy-card__title">Site Preparation — Ballast Delivery</p>
                        <p class="zy-card__body">In progress · Nairobi</p>
                    </x-ui.card>

                    <x-ui.card interactive featured>
                        <x-media.cover :src="asset($walkway['path'])" :alt="$walkway['alt']" />
                        <p class="zy-card__eyebrow">Structural</p>
                        <p class="zy-card__title">Covered Walkway — Steel Frame</p>
                        <p class="zy-card__body">In progress · Nairobi</p>
                    </x-ui.card>
                @endif
            </div>
        </div>
    </section>

    @if ($featuredArticles->isNotEmpty())
        <section class="zy-section">
            <div class="zy-container">
                <div class="zy-section__header">
                    <p class="zy-section__eyebrow">Knowledge Centre</p>
                    <h2>Guides from the field</h2>
                    <p>Practical construction advice for Kenyan homeowners and developers.</p>
                </div>
                <div class="zy-grid zy-grid--3">
                    @foreach ($featuredArticles->take(3) as $article)
                        <x-knowledge.card :article="$article" />
                    @endforeach
                </div>
                <p style="margin-top: var(--zy-space-6);">
                    <a href="{{ route('knowledge.index') }}" class="zy-btn zy-btn--secondary">Browse all articles</a>
                </p>
            </div>
        </section>
    @endif

    <x-sections.cta id="quote">
        <a href="{{ route('contact') }}" class="zy-btn zy-btn--primary zy-btn--lg">Request a Quote</a>
        <a href="{{ route('projects.index') }}" class="zy-btn zy-btn--frost zy-btn--lg">Browse Projects</a>
    </x-sections.cta>
@endsection
