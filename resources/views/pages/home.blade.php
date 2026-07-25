@extends('layouts.website')

@section('title', 'Zytech Contractors — Precision-built spaces')
@section('page-class', 'zy-page-home')

@section('content')
    <x-sections.hero />

    <section class="zy-section">
        <div class="zy-container">
            <div class="zy-section__header">
                <p class="zy-eyebrow">Featured work</p>
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

                <x-ui.card featured>
                    <div class="zy-card__media" aria-hidden="true"></div>
                    <p class="zy-card__eyebrow">Featured</p>
                    <p class="zy-card__title">Karen Courtyard Renovation</p>
                    <p class="zy-card__body">Interior · Exterior · Approvals</p>
                </x-ui.card>
            </div>
        </div>
    </section>

    <section class="zy-section" id="quote" style="background: var(--zy-color-surface); border-block: 1px solid var(--zy-color-border);">
        <div class="zy-container" style="display: grid; gap: var(--zy-space-6); max-width: var(--zy-max-width-narrow);">
            <div class="zy-section__header" style="margin-bottom: 0;">
                <p class="zy-eyebrow">Next step</p>
                <h2>Request a quotation</h2>
                <p>Wizard logic comes later — this CTA is styled with the design system only.</p>
            </div>
            <div>
                <a href="{{ route('styleguide') }}" class="zy-btn zy-btn--primary">Explore the design system</a>
            </div>
        </div>
    </section>
@endsection
