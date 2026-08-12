@php
    $images = config('zyntech-media.images');
    $videos = config('zyntech-media.videos');
    $image = $project->imageKey && isset($images[$project->imageKey])
        ? $images[$project->imageKey]
        : $images['paving_gravel_leveling'];
    $video = $project->videoKey && isset($videos[$project->videoKey]) ? $videos[$project->videoKey] : null;
@endphp

<div>
    <div class="zy-container zy-projects-intro">
        @if ($video)
            <x-media.banner
                :video="asset($video['path'])"
                :poster="asset($video['poster'] ?? $image['path'])"
                :alt="$video['alt']"
            >
                <p class="zy-eyebrow" style="color: rgb(255 255 255 / 0.75);">{{ $project->categoryName }}</p>
                <h1 style="color: #fff;">{{ $project->title }}</h1>
                <p>{{ $project->locationSummary }}</p>
            </x-media.banner>
        @else
            <x-media.banner :src="asset($image['path'])" :alt="$image['alt']">
                <p class="zy-eyebrow" style="color: rgb(255 255 255 / 0.75);">{{ $project->categoryName }}</p>
                <h1 style="color: #fff;">{{ $project->title }}</h1>
                <p>{{ $project->locationSummary }}</p>
            </x-media.banner>
        @endif
    </div>

    <section class="zy-section">
        <div class="zy-container zy-project-detail">
            <div class="zy-project-detail__copy">
                <p class="zy-section__eyebrow">{{ $project->type->label() }}</p>
                <h2>{{ $project->title }}</h2>
                <p>{{ $project->body ?: $project->excerpt }}</p>
                @if ($project->caseStudy)
                    <div class="zy-project-case-study">
                        <p class="zy-card__eyebrow">Case study</p>
                        <p>{{ $project->caseStudy }}</p>
                    </div>
                @endif
                <div class="zy-project-progress">
                    <span>Progress</span>
                    <div class="zy-project-progress__bar" role="progressbar" aria-valuenow="{{ $project->progressPercent }}" aria-valuemin="0" aria-valuemax="100">
                        <span style="width: {{ $project->progressPercent }}%;"></span>
                    </div>
                    <p>{{ $project->progressPercent }}% · {{ $project->constructionStage->label() }}</p>
                </div>
            </div>
            <aside class="zy-project-meta">
                <p class="zy-card__eyebrow">Location</p>
                <p class="zy-card__title">{{ $project->city ?: $project->county ?: 'Kenya' }}</p>
                @if ($project->completionYear)
                    <p class="zy-card__body">Completed {{ $project->completionYear }}</p>
                @endif
                <a href="{{ route('contact') }}" class="zy-btn zy-btn--primary">Discuss a similar project</a>
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

    @if (count($project->milestones) > 0)
        <section class="zy-section">
            <div class="zy-container">
                <div class="zy-section__header">
                    <p class="zy-section__eyebrow">Timeline</p>
                    <h2>Construction milestones</h2>
                </div>
                <ol class="zy-project-timeline">
                    @foreach ($project->milestones as $index => $milestone)
                        <li class="{{ $milestone->status->value === 'completed' ? 'is-complete' : '' }}">
                            <span>{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                            <div>
                                <p class="zy-card__title">{{ $milestone->title }}</p>
                                <p class="zy-card__body">{{ $milestone->description }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>
    @endif

    @if ($galleryItems->isNotEmpty())
        <section class="zy-section zy-section--alt">
            <div class="zy-container">
                <div class="zy-section__header">
                    <p class="zy-section__eyebrow">Gallery</p>
                    <h2>On-site photography</h2>
                </div>
                <div class="zy-grid zy-grid--3">
                    @foreach ($galleryItems as $item)
                        @php $galleryImage = $images[$item->image_key] ?? null; @endphp
                        @if ($galleryImage)
                            <x-ui.card>
                                <x-media.cover :src="asset($galleryImage['path'])" :alt="$galleryImage['alt']" />
                                @if ($item->caption)
                                    <p class="zy-card__body">{{ $item->caption }}</p>
                                @endif
                            </x-ui.card>
                        @endif
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($beforeAfter->isNotEmpty())
        <section class="zy-section">
            <div class="zy-container">
                <div class="zy-section__header">
                    <p class="zy-section__eyebrow">Before &amp; after</p>
                    <h2>What changed on site</h2>
                </div>
                <div class="zy-project-comparisons">
                    @foreach ($beforeAfter as $comparison)
                        @php
                            $before = $images[$comparison->before_image_key] ?? null;
                            $after = $images[$comparison->after_image_key] ?? null;
                        @endphp
                        @if ($before && $after)
                            <div class="zy-project-compare" x-data="{ position: 50 }">
                                <div class="zy-project-compare__stage">
                                    <img src="{{ asset($after['path']) }}" alt="{{ $after['alt'] }}">
                                    <div class="zy-project-compare__before" :style="`width: ${position}%`">
                                        <img src="{{ asset($before['path']) }}" alt="{{ $before['alt'] }}">
                                    </div>
                                </div>
                                <input type="range" min="0" max="100" x-model="position" aria-label="Compare before and after">
                                @if ($comparison->caption)
                                    <p class="zy-card__title">{{ $comparison->caption }}</p>
                                @endif
                                @if ($comparison->description)
                                    <p class="zy-card__body">{{ $comparison->description }}</p>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($progressUpdates->isNotEmpty())
        <section class="zy-section zy-section--alt">
            <div class="zy-container">
                <div class="zy-section__header">
                    <p class="zy-section__eyebrow">Progress</p>
                    <h2>Site updates</h2>
                </div>
                <div class="zy-project-updates">
                    @foreach ($progressUpdates as $update)
                        @php $updateImage = $update->image_key ? ($images[$update->image_key] ?? null) : null; @endphp
                        <x-ui.card>
                            @if ($updateImage)
                                <x-media.cover :src="asset($updateImage['path'])" :alt="$updateImage['alt']" />
                            @endif
                            <p class="zy-card__title">{{ $update->title }}</p>
                            @if ($update->body)
                                <p class="zy-card__body">{{ $update->body }}</p>
                            @endif
                            @if ($update->progress_percent !== null)
                                <p class="zy-card__eyebrow">{{ $update->progress_percent }}% complete</p>
                            @endif
                        </x-ui.card>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($serviceCards->isNotEmpty())
        <section class="zy-section">
            <div class="zy-container">
                <div class="zy-section__header">
                    <p class="zy-section__eyebrow">Services</p>
                    <h2>Disciplines on this project</h2>
                </div>
                <div class="zy-grid zy-grid--3">
                    @foreach ($serviceCards as $service)
                        <x-services.card :service="$service" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <livewire:project.related-projects :project-id="$model->id" />

    <x-sections.cta>
        <a href="{{ route('contact') }}" class="zy-btn zy-btn--primary zy-btn--lg">Request a Quote</a>
        <a href="{{ route('projects.index') }}" class="zy-btn zy-btn--frost zy-btn--lg">All projects</a>
    </x-sections.cta>
</div>
