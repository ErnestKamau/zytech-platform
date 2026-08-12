@php
    $images = config('zyntech-media.images');
    $courtyard = $images['commercial_courtyard'];
    $walkway = $images['structural_walkway'];
@endphp

<div>

@if ($profile === null)
    <section class="zy-section">
        <div class="zy-container">
            <div class="zy-section__header">
                <p class="zy-section__eyebrow">About</p>
                <h1>Zytech Contractors</h1>
                <p>Company profile is being prepared. Check back shortly, or get in touch for a site visit.</p>
            </div>
            <a href="{{ route('contact') }}" class="zy-btn zy-btn--primary">Contact us</a>
        </div>
    </section>
@else
    <div class="zy-container zy-about-intro">
        <x-media.banner :src="asset($courtyard['path'])" :alt="$courtyard['alt']">
            <p class="zy-eyebrow" style="color: rgb(255 255 255 / 0.75);">About</p>
            <h1 style="color: #fff;">{{ $profile->name }}</h1>
            <p>{{ $profile->tagline ?: $profile->shortDescription }}</p>
        </x-media.banner>
    </div>

    <section class="zy-section">
        <div class="zy-container">
            <x-media.gallery
                :left-src="asset($walkway['path'])"
                :left-alt="$walkway['alt']"
                :right-src="asset($courtyard['path'])"
                :right-alt="$courtyard['alt']"
            >
                <p class="zy-section__eyebrow">Who we are</p>
                <h2>{{ $profile->motto ?: 'If you can plan it, we can build it.' }}</h2>
                <p>{{ $profile->about ?: $profile->shortDescription }}</p>
            </x-media.gallery>
        </div>
    </section>

    @if ($profile->mission || $profile->vision)
        <section class="zy-section zy-section--alt">
            <div class="zy-container">
                <div class="zy-grid zy-grid--2">
                    @if ($profile->mission)
                        <x-ui.card>
                            <p class="zy-card__eyebrow">Mission</p>
                            <p class="zy-card__title">What we build toward</p>
                            <p class="zy-card__body">{{ $profile->mission }}</p>
                        </x-ui.card>
                    @endif
                    @if ($profile->vision)
                        <x-ui.card>
                            <p class="zy-card__eyebrow">Vision</p>
                            <p class="zy-card__title">Where we are headed</p>
                            <p class="zy-card__body">{{ $profile->vision }}</p>
                        </x-ui.card>
                    @endif
                </div>
            </div>
        </section>
    @endif

    @if ($profile->history || $profile->whyChooseUs || count($profile->coreValues) > 0)
        <section class="zy-section">
            <div class="zy-container zy-about-story">
                @if ($profile->history)
                    <div>
                        <p class="zy-section__eyebrow">History</p>
                        <h2>Built on Kenyan soil</h2>
                        <p>{{ $profile->history }}</p>
                    </div>
                @endif

                @if ($profile->whyChooseUs)
                    <div>
                        <p class="zy-section__eyebrow">Why Zytech</p>
                        <h2>One accountable crew</h2>
                        <p>{{ $profile->whyChooseUs }}</p>
                    </div>
                @endif

                @if (count($profile->coreValues) > 0)
                    <ul class="zy-about-values">
                        @foreach ($profile->coreValues as $value)
                            <li>{{ $value }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </section>
    @endif

    @if ($statistics->isNotEmpty())
        <section class="zy-section zy-section--alt" style="padding-block: var(--zy-space-12);">
            <div class="zy-container">
                <div class="zy-stats">
                    @foreach ($statistics as $stat)
                        <div class="zy-stat">
                            <p class="zy-stat__value" style="font-size: var(--zy-text-3xl);">{{ $stat->value }}</p>
                            <p class="zy-stat__label">{{ $stat->label }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($leadership->isNotEmpty())
        <section class="zy-section">
            <div class="zy-container">
                <div class="zy-section__header">
                    <p class="zy-section__eyebrow">Leadership</p>
                    <h2>The people accountable for the work</h2>
                </div>
                <div class="zy-grid zy-grid--3">
                    @foreach ($leadership as $member)
                        <x-ui.card>
                            @if ($member->photo_url)
                                <x-media.cover :src="$member->photo_url" :alt="$member->name" />
                            @endif
                            <p class="zy-card__eyebrow">{{ $member->position }}</p>
                            <p class="zy-card__title">{{ $member->name }}</p>
                            @if ($member->biography)
                                <p class="zy-card__body">{{ $member->biography }}</p>
                            @endif
                        </x-ui.card>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($branches->isNotEmpty())
        <section class="zy-section zy-section--alt">
            <div class="zy-container">
                <div class="zy-section__header">
                    <p class="zy-section__eyebrow">Offices</p>
                    <h2>Where to find us</h2>
                </div>
                <div class="zy-grid zy-grid--3">
                    @foreach ($branches as $branch)
                        <x-ui.card>
                            <p class="zy-card__eyebrow">{{ $branch->type->label() }}</p>
                            <p class="zy-card__title">{{ $branch->name }}</p>
                            <p class="zy-card__body">
                                {{ collect([$branch->address, $branch->city, $branch->county])->filter()->implode(', ') }}
                                @if ($branch->phone)
                                    <br>{{ $branch->phone }}
                                @endif
                            </p>
                        </x-ui.card>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($certifications->isNotEmpty() || $awards->isNotEmpty())
        <section class="zy-section">
            <div class="zy-container">
                <div class="zy-section__header">
                    <p class="zy-section__eyebrow">Credentials</p>
                    <h2>Certifications and awards</h2>
                </div>
                <div class="zy-grid zy-grid--2">
                    @if ($certifications->isNotEmpty())
                        <div>
                            <h3 class="zy-about-subhead">Certifications</h3>
                            <ul class="zy-about-list">
                                @foreach ($certifications as $certification)
                                    <li>
                                        <strong>{{ $certification->name }}</strong>
                                        <span>{{ collect([$certification->issuer, $certification->status->label()])->filter()->implode(' · ') }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if ($awards->isNotEmpty())
                        <div>
                            <h3 class="zy-about-subhead">Awards</h3>
                            <ul class="zy-about-list">
                                @foreach ($awards as $award)
                                    <li>
                                        <strong>{{ $award->title }}</strong>
                                        <span>{{ collect([$award->year, $award->issuer, $award->category->label()])->filter()->implode(' · ') }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    @if ($partners->isNotEmpty())
        <section class="zy-section zy-section--alt">
            <div class="zy-container">
                <div class="zy-section__header">
                    <p class="zy-section__eyebrow">Partners</p>
                    <h2>People we build with</h2>
                </div>
                <div class="zy-grid zy-grid--3">
                    @foreach ($partners as $partner)
                        <x-ui.card>
                            <p class="zy-card__title">{{ $partner->name }}</p>
                            @if ($partner->description)
                                <p class="zy-card__body">{{ $partner->description }}</p>
                            @endif
                        </x-ui.card>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($testimonials->isNotEmpty())
        <section class="zy-section">
            <div class="zy-container">
                <div class="zy-section__header">
                    <p class="zy-section__eyebrow">Clients</p>
                    <h2>What principals say</h2>
                </div>
                <div class="zy-grid zy-grid--2">
                    @foreach ($testimonials as $testimonial)
                        <x-ui.card :featured="$testimonial->is_featured">
                            <p class="zy-card__body zy-about-quote">“{{ $testimonial->quote }}”</p>
                            <p class="zy-card__title">{{ $testimonial->author_name }}</p>
                            <p class="zy-card__eyebrow">{{ collect([$testimonial->author_role, $testimonial->company_name])->filter()->implode(' · ') }}</p>
                        </x-ui.card>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($faqs->isNotEmpty())
        <section class="zy-section zy-section--alt">
            <div class="zy-container">
                <div class="zy-section__header">
                    <p class="zy-section__eyebrow">FAQs</p>
                    <h2>Questions we hear on site</h2>
                </div>
                <div class="zy-about-faqs" x-data="{ open: 0 }">
                    @foreach ($faqs as $index => $faq)
                        <div class="zy-about-faq">
                            <button
                                type="button"
                                class="zy-about-faq__q"
                                @click="open = open === {{ $index }} ? null : {{ $index }}"
                                :aria-expanded="open === {{ $index }}"
                            >
                                {{ $faq->question }}
                            </button>
                            <div class="zy-about-faq__a" x-show="open === {{ $index }}" x-cloak>
                                <p>{{ $faq->answer }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <x-sections.cta>
        <a href="{{ route('contact') }}" class="zy-btn zy-btn--primary zy-btn--lg">Request a Quote</a>
        <a href="{{ route('projects.index') }}" class="zy-btn zy-btn--frost zy-btn--lg">Browse Projects</a>
    </x-sections.cta>
@endif
</div>
