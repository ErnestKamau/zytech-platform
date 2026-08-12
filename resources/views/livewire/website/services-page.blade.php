@php
    $images = config('zyntech-media.images');
    $banner = $images['paving_gravel_leveling'];
    $process = config('zyntech-media.videos.services_process');
@endphp

<div>
    <div class="zy-container zy-services-intro">
        <x-media.banner :src="asset($banner['path'])" :alt="$banner['alt']">
            <p class="zy-eyebrow" style="color: rgb(255 255 255 / 0.75);">What we do</p>
            <h1 style="color: #fff;">{{ $selectedCategory?->name ?? 'Services' }}</h1>
            <p>{{ $selectedCategory?->description ?: 'Design, estimate, approve, and build — one team on Kenyan soil from first sketch to handover.' }}</p>
        </x-media.banner>
    </div>

    @if ($categories->isNotEmpty())
        <nav class="zy-container zy-service-cats" aria-label="Service categories">
            <a
                href="{{ route('services.index') }}"
                class="zy-service-cat {{ $selectedCategory === null ? 'is-active' : '' }}"
            >All</a>
            @foreach ($categories as $item)
                <a
                    href="{{ route('services.category', $item->slug) }}"
                    class="zy-service-cat {{ $selectedCategory?->slug === $item->slug ? 'is-active' : '' }}"
                >{{ $item->name }}</a>
            @endforeach
        </nav>
    @endif

    @if ($selectedCategory === null)
        <livewire:service.featured-services />
    @endif

    <section class="zy-section">
        <div class="zy-container">
            @if ($selectedCategory === null)
                <div class="zy-section__header">
                    <p class="zy-section__eyebrow">Catalogue</p>
                    <h2>Every discipline under one roof</h2>
                </div>
            @endif
            @if ($services->isEmpty())
                <p>No published services in this category yet.</p>
            @else
                <div class="zy-grid zy-grid--3">
                    @foreach ($services as $service)
                        <x-services.card :service="$service" />
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section class="zy-section zy-section--alt">
        <div class="zy-container">
            <div class="zy-section__header">
                <p class="zy-section__eyebrow">On site</p>
                <h2>How the work actually looks</h2>
                <p>Crew, materials, and structure — filmed on a live Zytech project.</p>
            </div>
            <x-media.banner
                class="zy-services-process"
                :video="asset($process['path'])"
                :poster="asset($process['poster'])"
                :alt="$process['alt']"
            >
                <p class="zy-eyebrow" style="color: rgb(255 255 255 / 0.75);">Process</p>
                <h2 style="color: #fff;">From groundworks to structure</h2>
            </x-media.banner>
        </div>
    </section>

    <x-sections.cta>
        <a href="{{ route('contact') }}" class="zy-btn zy-btn--primary zy-btn--lg">Request a Quote</a>
        <a href="{{ route('projects.index') }}" class="zy-btn zy-btn--frost zy-btn--lg">Browse Projects</a>
    </x-sections.cta>
</div>
