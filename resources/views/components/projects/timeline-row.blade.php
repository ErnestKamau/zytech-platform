@props(['project', 'index' => 0])

@php
    $images = config('zyntech-media.images');
    $image = $project->imageKey && isset($images[$project->imageKey])
        ? $images[$project->imageKey]
        : null;

    $started = $project->startedOn ? \Illuminate\Support\Carbon::parse($project->startedOn)->format('M Y') : null;
    $completed = $project->completedOn ? \Illuminate\Support\Carbon::parse($project->completedOn)->format('M Y') : null;

    $duration = match (true) {
        $started && $completed => "{$started} — {$completed}",
        $started && ! $completed => "Started {$started} · in progress",
        default => $project->statusLabel,
    };
@endphp

<li class="zy-projects-timeline__row {{ $index % 2 === 1 ? 'is-reversed' : '' }}">
    <a href="{{ route('projects.show', $project->slug) }}" class="zy-projects-timeline__media">
        @if ($image)
            <img src="{{ asset($image['path']) }}" alt="{{ $image['alt'] }}" loading="lazy">
        @endif
        @if ($project->isFeatured)
            <span class="zy-projects-timeline__badge">Featured</span>
        @endif
    </a>

    <div class="zy-projects-timeline__marker" aria-hidden="true">
        <span class="zy-projects-timeline__node"></span>
    </div>

    <div class="zy-projects-timeline__body">
        <p class="zy-card__eyebrow">{{ $project->categoryName }}</p>
        <a href="{{ route('projects.show', $project->slug) }}" class="zy-projects-timeline__title-link">
            <h3 class="zy-projects-timeline__title">{{ $project->title }}</h3>
        </a>
        <p class="zy-card__body">{{ $project->excerpt ?: $project->locationSummary }}</p>
        <p class="zy-projects-timeline__duration">{{ $duration }}</p>

        <x-projects.stage-stepper :stage="$project->constructionStage" compact />

        <a href="{{ route('projects.show', $project->slug) }}" class="zy-projects-timeline__link">
            View project <span aria-hidden="true">&rarr;</span>
        </a>
    </div>
</li>
