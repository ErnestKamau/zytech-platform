@php
    $images = config('zyntech-media.images');
    $stillKeys = config('zyntech-media.service_stills.'.$service->slug, []);
    $storyStills = collect(is_array($stillKeys) ? $stillKeys : [])
        ->map(fn (string $key) => $images[$key] ?? null)
        ->filter()
        ->values();

    if ($storyStills->isEmpty() && $service->imageKey && isset($images[$service->imageKey])) {
        $storyStills = collect([$images[$service->imageKey]]);
    }

    if ($storyStills->isEmpty()) {
        $storyStills = collect([$images['commercial_courtyard']]);
    }

    $bannerImage = $storyStills->first();
    $gallery = collect($service->galleryKeys)
        ->map(fn (string $key) => $images[$key] ?? null)
        ->filter()
        ->values();
    $slideCount = $storyStills->count();
@endphp

<div>
    <div class="zy-container zy-services-intro">
        <x-media.banner :src="asset($bannerImage['path'])" :alt="$bannerImage['alt']">
            <p class="zy-eyebrow" style="color: rgb(255 255 255 / 0.75);">{{ $service->categoryName }}</p>
            <h1 style="color: #fff;">{{ $service->title }}</h1>
            <p>{{ $service->excerpt }}</p>
        </x-media.banner>
    </div>

    <section class="zy-section">
        <div class="zy-container zy-service-detail">
            <div
                class="zy-service-detail__copy"
                x-data="{
                    i: 0,
                    n: {{ $slideCount }},
                    start() {
                        if (this.n < 2) return;
                        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
                        setInterval(() => { this.i = (this.i + 1) % this.n }, 6000);
                    },
                }"
                x-init="start()"
            >
                <div class="zy-service-detail__slides" aria-hidden="true">
                    @foreach ($storyStills as $index => $still)
                        <img
                            src="{{ asset($still['path']) }}"
                            alt=""
                            :class="{ 'is-active': i === {{ $index }} }"
                        >
                    @endforeach
                </div>
                <div class="zy-service-detail__scrim"></div>
                <div class="zy-service-detail__text">
                    <p class="zy-section__eyebrow">{{ $service->type->label() }}</p>
                    <h2>{{ $service->title }}</h2>
                    <p>{{ $service->body ?: $service->excerpt }}</p>
                </div>
                @if ($slideCount > 1)
                    <div class="zy-service-detail__dots" role="tablist" aria-label="Photos">
                        @foreach ($storyStills as $index => $still)
                            <button
                                type="button"
                                class="zy-service-detail__dot"
                                :class="{ 'is-active': i === {{ $index }} }"
                                :aria-selected="(i === {{ $index }}).toString()"
                                aria-label="Show photo {{ $index + 1 }}"
                                @click="i = {{ $index }}"
                            ></button>
                        @endforeach
                    </div>
                @endif
            </div>
            <aside class="zy-service-quote">
                <p class="zy-card__eyebrow">Pricing</p>
                <p class="zy-card__title">{{ $service->pricingModel->label() }}</p>
                @if ($service->priceAmount)
                    <p class="zy-service-quote__price">
                        {{ $service->priceCurrency }} {{ number_format((float) $service->priceAmount) }}
                        @if ($service->priceUnit)
                            <span>{{ $service->priceUnit }}</span>
                        @endif
                    </p>
                @endif
                @if ($service->pricingNotes)
                    <p class="zy-card__body">{{ $service->pricingNotes }}</p>
                @endif
                <a href="{{ route('contact') }}" class="zy-btn zy-btn--primary">Request a Quote</a>
            </aside>
        </div>
    </section>

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

    @if (count($service->features) > 0)
        <section class="zy-section">
            <div class="zy-container">
                <div class="zy-section__header">
                    <p class="zy-section__eyebrow">Included</p>
                    <h2>What this service covers</h2>
                </div>
                <div class="zy-grid zy-grid--3">
                    @foreach ($service->features as $feature)
                        <x-ui.card>
                            <p class="zy-card__title">{{ $feature->title }}</p>
                            <p class="zy-card__body">{{ $feature->description }}</p>
                        </x-ui.card>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if (count($service->processes) > 0)
        <section class="zy-section zy-section--alt">
            <div class="zy-container">
                <div class="zy-section__header">
                    <p class="zy-section__eyebrow">Process</p>
                    <h2>How we deliver it</h2>
                </div>
                <ol class="zy-service-steps">
                    @foreach ($service->processes as $index => $step)
                        <li>
                            <span>{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                            <div>
                                <p class="zy-card__title">{{ $step->title }}</p>
                                <p class="zy-card__body">{{ $step->description }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>
    @endif

    @if ($gallery->count() >= 2)
        <section class="zy-section">
            <div class="zy-container">
                <x-media.gallery
                    :left-src="asset($gallery[0]['path'])"
                    :left-alt="$gallery[0]['alt']"
                    :right-src="asset($gallery[1]['path'])"
                    :right-alt="$gallery[1]['alt']"
                >
                    <p class="zy-section__eyebrow">Gallery</p>
                    <h2>Work from this discipline</h2>
                    <p>Photographed on Zytech sites — files stay in the public media library.</p>
                </x-media.gallery>
            </div>
        </section>
    @elseif ($gallery->count() === 1)
        <section class="zy-section">
            <div class="zy-container">
                <x-media.cover
                    :src="asset($gallery[0]['path'])"
                    :alt="$gallery[0]['alt']"
                />
            </div>
        </section>
    @endif

    @if ($linkedProjects->isNotEmpty() || $teaserProjects->isNotEmpty())
        <section class="zy-section zy-section--alt">
            <div class="zy-container">
                <div class="zy-section__header">
                    <p class="zy-section__eyebrow">Related projects</p>
                    <h2>Where this service shows up on site</h2>
                </div>
                <div class="zy-grid zy-grid--3">
                    @if ($linkedProjects->isNotEmpty())
                        @foreach ($linkedProjects as $project)
                            <x-projects.card :project="$project" />
                        @endforeach
                    @else
                        @foreach ($teaserProjects as $project)
                            @php
                                $projectImage = $project->image_key && isset($images[$project->image_key])
                                    ? $images[$project->image_key]
                                    : null;
                            @endphp
                            <x-ui.card interactive>
                                @if ($projectImage)
                                    <x-media.cover
                                        :src="asset($projectImage['path'])"
                                        :alt="$projectImage['alt']"
                                    />
                                @endif
                                <p class="zy-card__title" style="margin-top: var(--zy-space-2);">{{ $project->title }}</p>
                                @if ($project->summary)
                                    <p class="zy-card__body">{{ $project->summary }}</p>
                                @endif
                            </x-ui.card>
                        @endforeach
                    @endif
                </div>
            </div>
        </section>
    @endif

    @if ($linkedArticles->isNotEmpty())
        <section class="zy-section">
            <div class="zy-container">
                <div class="zy-section__header">
                    <p class="zy-section__eyebrow">Related reading</p>
                    <h2>Knowledge Centre guides for this service</h2>
                </div>
                <div class="zy-grid zy-grid--3">
                    @foreach ($linkedArticles as $article)
                        <x-knowledge.card :article="$article" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <livewire:service.faqs :service-id="$model->id" />

    <livewire:service.related-services :service-id="$model->id" />

    <x-sections.cta>
        <a href="{{ route('contact') }}" class="zy-btn zy-btn--primary zy-btn--lg">Request a Quote</a>
        <a href="{{ route('services.index') }}" class="zy-btn zy-btn--frost zy-btn--lg">All services</a>
    </x-sections.cta>
</div>
