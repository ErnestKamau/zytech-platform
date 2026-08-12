@props(['article'])

@php
    $images = config('zyntech-media.images');
    $image = $article->imageKey && isset($images[$article->imageKey])
        ? $images[$article->imageKey]
        : null;
@endphp

<a href="{{ route('knowledge.show', $article->slug) }}" class="zy-knowledge-card-link">
    <x-ui.card interactive :featured="$article->isFeatured">
        @if ($image)
            <x-media.cover :src="asset($image['path'])" :alt="$image['alt']" />
        @endif
        <p class="zy-card__eyebrow">{{ $article->categoryName }}</p>
        <p class="zy-card__title">{{ $article->title }}</p>
        <p class="zy-card__body">{{ $article->excerpt }}</p>
        <p class="zy-card__meta">{{ $article->readingTimeMinutes }} min read · {{ $article->type->label() }}</p>
    </x-ui.card>
</a>
