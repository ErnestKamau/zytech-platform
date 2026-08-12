@props(['project'])

@php
    $images = config('zyntech-media.images');
    $image = $project->imageKey && isset($images[$project->imageKey])
        ? $images[$project->imageKey]
        : null;
@endphp

<a href="{{ route('projects.show', $project->slug) }}" class="zy-project-card-link">
    <x-ui.card interactive :featured="$project->isFeatured">
        @if ($image)
            <x-media.cover :src="asset($image['path'])" :alt="$image['alt']" />
        @endif
        <p class="zy-card__eyebrow">{{ $project->categoryName }}</p>
        <p class="zy-card__title">{{ $project->title }}</p>
        <p class="zy-card__body">{{ $project->locationSummary }}</p>
    </x-ui.card>
</a>
