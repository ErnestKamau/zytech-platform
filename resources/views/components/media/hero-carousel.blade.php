@props(['slides' => []])

@php($slides = collect($slides)->values())

<div
    {{ $attributes->class('zy-media zy-media-hero zy-hero-carousel') }}
    x-data="{
        active: 0,
        total: {{ $slides->count() }},
        timer: null,
        reducedMotion: window.matchMedia('(prefers-reduced-motion: reduce)').matches,
        start() {
            if (this.reducedMotion || this.total <= 1) return;
            this.stop();
            this.timer = setInterval(() => this.next(), 7000);
        },
        stop() { clearInterval(this.timer); },
        next() { this.active = (this.active + 1) % this.total; },
        prev() { this.active = (this.active - 1 + this.total) % this.total; },
        goTo(i) { this.active = i; },
    }"
    x-init="start()"
    @mouseenter="stop()"
    @mouseleave="start()"
    @focusin="stop()"
    @focusout="start()"
    aria-roledescription="carousel"
>
    @foreach ($slides as $index => $slide)
        <div
            class="zy-hero-carousel__slide"
            x-show="active === {{ $index }}"
            x-cloak
            aria-hidden="{{ $index === 0 ? 'false' : 'true' }}"
        >
            @if ($slide->mediaType === 'video')
                <video
                    muted
                    loop
                    playsinline
                    preload="none"
                    x-ref="video{{ $index }}"
                    x-bind:preload="active === {{ $index }} ? 'auto' : 'none'"
                    poster="{{ $slide->posterUrl }}"
                    x-effect="
                        if (active === {{ $index }}) {
                            $refs.video{{ $index }}.load();
                            $refs.video{{ $index }}.play().catch(() => {});
                        } else {
                            $refs.video{{ $index }}.pause();
                        }
                    "
                >
                    <template x-if="active === {{ $index }}">
                        <source src="{{ $slide->videoUrl }}" type="video/mp4">
                    </template>
                </video>
                @if ($slide->posterUrl)
                    <img src="{{ $slide->posterUrl }}" alt="{{ $slide->altText }}" class="zy-media-hero__fallback">
                @endif
            @elseif ($slide->imageUrl)
                <img src="{{ $slide->imageUrl }}" alt="{{ $slide->altText }}" loading="{{ $index === 0 ? 'eager' : 'lazy' }}">
            @endif
        </div>
    @endforeach

    <div class="zy-media-hero__scrim"></div>

    @if ($slides->count() > 1)
        <div class="zy-hero-carousel__controls">
            <button type="button" class="zy-icon-btn zy-icon-btn--frost" @click="prev()" aria-label="Previous slide">‹</button>
            <div class="zy-hero-carousel__dots" role="tablist">
                @foreach ($slides as $index => $slide)
                    <button
                        type="button"
                        class="zy-hero-carousel__dot"
                        :class="{ 'is-active': active === {{ $index }} }"
                        @click="goTo({{ $index }})"
                        aria-label="Go to slide {{ $index + 1 }}"
                    ></button>
                @endforeach
            </div>
            <button type="button" class="zy-icon-btn zy-icon-btn--frost" @click="next()" aria-label="Next slide">›</button>
        </div>
    @endif
</div>
