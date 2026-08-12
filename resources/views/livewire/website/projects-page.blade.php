@php
    $images = config('zyntech-media.images');
    $showreel = config('zyntech-media.videos.projects_showreel');
@endphp

<div>
    <div class="zy-container zy-projects-intro">
        <x-media.banner
            :video="asset($showreel['path'])"
            :poster="asset($showreel['poster'])"
            :alt="$showreel['alt']"
        >
            <p class="zy-eyebrow" style="color: rgb(255 255 255 / 0.75);">Portfolio</p>
            <h1 style="color: #fff;">{{ $selectedCategory?->name ?? 'Projects' }}</h1>
            <p>{{ $selectedCategory?->description ?: 'Work photographed on Zytech sites across Nairobi and Kiambu — from groundworks to finished courtyards.' }}</p>
        </x-media.banner>
    </div>

    @if ($categories->isNotEmpty())
        <nav class="zy-container zy-project-cats" aria-label="Project categories">
            <a href="{{ route('projects.index') }}" class="zy-project-cat {{ $selectedCategory === null ? 'is-active' : '' }}">All</a>
            @foreach ($categories as $item)
                <a
                    href="{{ route('projects.category', $item->slug) }}"
                    class="zy-project-cat {{ $selectedCategory?->slug === $item->slug ? 'is-active' : '' }}"
                >{{ $item->name }}</a>
            @endforeach
        </nav>
    @endif

    @if ($selectedCategory === null)
        <livewire:project.featured-projects />
    @endif

    <div class="zy-container zy-projects-grid">
        @if ($projects->isEmpty())
            <x-ui.empty-state
                title="No projects in this category"
                description="We have not published work in this category yet. Explore the full portfolio or request a quote for a similar brief."
                :lottie="asset('media/lottie/no-connection.lottie')"
            >
                <x-slot:actions>
                    <a href="{{ route('projects.index') }}" class="zy-btn zy-btn--primary">All projects</a>
                    <a href="{{ route('quote.index') }}" class="zy-btn zy-btn--secondary">Request a quote</a>
                </x-slot:actions>
            </x-ui.empty-state>
        @else
            <div class="zy-grid zy-grid--3">
                @foreach ($projects as $project)
                    <x-projects.card :project="$project" />
                @endforeach
            </div>
        @endif
    </div>

    @if ($mapMarkers->isNotEmpty())
        <section class="zy-section zy-section--alt">
            <div class="zy-container">
                <div class="zy-section__header">
                    <p class="zy-section__eyebrow">Across Kenya</p>
                    <h2>Where we build</h2>
                    <p>Published project locations. Full interactive map support lands in a later pass.</p>
                </div>
                <div class="zy-project-map">
                    @foreach ($mapMarkers as $marker)
                        @php
                            $markerImage = $marker['image_key'] && isset($images[$marker['image_key']])
                                ? $images[$marker['image_key']]
                                : null;
                        @endphp
                        <a href="{{ route('projects.show', $marker['slug']) }}" class="zy-project-map__item">
                            @if ($markerImage)
                                <img src="{{ asset($markerImage['path']) }}" alt="{{ $markerImage['alt'] }}" loading="lazy">
                            @endif
                            <div>
                                <p class="zy-card__title">{{ $marker['title'] }}</p>
                                <p class="zy-card__body">{{ $marker['summary'] }}</p>
                                <p class="zy-card__eyebrow">{{ $marker['county'] ?? 'Kenya' }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</div>
