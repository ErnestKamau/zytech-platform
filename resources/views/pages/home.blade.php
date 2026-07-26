@extends('layouts.website')

@section('title', 'Zytech Contractors — Precision-built spaces')
@section('page-class', 'zy-page-home')

@php
    $stats = [
        ['value' => '120+', 'label' => 'Projects delivered'],
        ['value' => '14', 'label' => 'Years in operation'],
        ['value' => '96%', 'label' => 'On-time completion'],
        ['value' => 'KES 2.4B', 'label' => 'Value under management'],
    ];

    $services = [
        [
            'title' => 'Interior Design',
            'body' => 'Space planning and execution that balances function with atmosphere.',
            'icon' => 'M2.25 12l8.954-8.955a1.5 1.5 0 012.122 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75',
            'featured' => false,
        ],
        [
            'title' => 'Exterior Design',
            'body' => 'Modern architectural facades engineered for the Kenyan climate.',
            'icon' => 'M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21',
            'featured' => true,
        ],
        [
            'title' => 'Plan Estimation',
            'body' => 'Accurate BOQs and budgets before a single stone is laid.',
            'icon' => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z',
            'featured' => false,
        ],
        [
            'title' => 'Property Sketching',
            'body' => 'Concept development and property sketches that sell the vision.',
            'icon' => 'M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10',
            'featured' => false,
        ],
        [
            'title' => 'Plan Approvals',
            'body' => 'Statutory approvals guided end-to-end, without the runaround.',
            'icon' => 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.75h-.152c-3.196 0-6.1-1.248-8.25-3.286z',
            'featured' => false,
        ],
        [
            'title' => 'Construction Management',
            'body' => 'One accountable team from groundbreaking to handover.',
            'icon' => 'M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085',
            'featured' => false,
        ],
    ];
@endphp

@section('content')
    <x-sections.hero />

    {{-- Stats band --}}
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

    {{-- Services --}}
    <section class="zy-section">
        <div class="zy-container">
            <div class="zy-section__header">
                <p class="zy-section__eyebrow">What we do</p>
                <h2>Every discipline under one roof</h2>
                <p>Design, estimate, approve, and build with one accountable team — no hand-offs, no finger-pointing.</p>
            </div>

            <div class="zy-grid zy-grid--3">
                @foreach ($services as $service)
                    <x-ui.card interactive>
                        <span class="zy-icon-tile {{ $service['featured'] ? 'zy-icon-tile--gradient' : '' }}" aria-hidden="true">
                            <svg class="zy-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $service['icon'] }}" />
                            </svg>
                        </span>
                        <p class="zy-card__title" style="margin-top: var(--zy-space-2);">{{ $service['title'] }}</p>
                        <p class="zy-card__body">{{ $service['body'] }}</p>
                    </x-ui.card>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Featured projects --}}
    <section class="zy-section zy-section--alt">
        <div class="zy-container">
            <div class="zy-section__header">
                <p class="zy-section__eyebrow">Featured work</p>
                <h2>Projects that speak for themselves</h2>
                <p>Placeholder covers until production photography is ready — layout and tokens stay fixed.</p>
            </div>

            <div class="zy-grid zy-grid--3">
                <x-ui.card interactive>
                    <div class="zy-card__media" aria-hidden="true"></div>
                    <p class="zy-card__eyebrow">Residential</p>
                    <p class="zy-card__title">Kilimani Modern Villa</p>
                    <p class="zy-card__body">Completed 2026 · Nairobi</p>
                </x-ui.card>

                <x-ui.card interactive>
                    <div class="zy-card__media" aria-hidden="true"></div>
                    <p class="zy-card__eyebrow">Commercial</p>
                    <p class="zy-card__title">Two Rivers Office Park</p>
                    <p class="zy-card__body">In progress · Nairobi</p>
                </x-ui.card>

                <x-ui.card interactive featured>
                    <div class="zy-card__media" aria-hidden="true"></div>
                    <p class="zy-card__eyebrow">Featured</p>
                    <p class="zy-card__title">Karen Courtyard Renovation</p>
                    <p class="zy-card__body">Interior · Exterior · Approvals</p>
                </x-ui.card>
            </div>
        </div>
    </section>

    {{-- CTA panel --}}
    <section class="zy-section" id="quote">
        <div class="zy-container">
            <div class="zy-cta">
                <p class="zy-section__eyebrow" style="color: var(--zy-sky-300);">Next step</p>
                <h2>Ready to build something that lasts?</h2>
                <p>Tell us about your project and get a detailed quotation from our estimation team within 48 hours.</p>
                <div class="zy-cta__actions">
                    <a href="#quote" class="zy-btn zy-btn--gradient zy-btn--lg">Request a Quote</a>
                    <a href="{{ route('projects.index') }}" class="zy-btn zy-btn--frost zy-btn--lg">Browse Projects</a>
                </div>
            </div>
        </div>
    </section>
@endsection
