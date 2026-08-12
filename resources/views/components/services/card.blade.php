@props(['service'])

@php
    $images = config('zyntech-media.images');
    $image = $service->imageKey && isset($images[$service->imageKey])
        ? $images[$service->imageKey]
        : null;
@endphp

<a href="{{ route('services.show', $service->slug) }}" class="zy-service-card-link">
    <x-ui.card interactive :featured="$service->isFeatured" id="{{ $service->slug }}">
        @if ($image)
            <x-media.cover
                :src="asset($image['path'])"
                :alt="$image['alt']"
            />
        @else
            <span class="zy-icon-tile {{ $service->isFeatured ? 'zy-icon-tile--gradient' : '' }}" aria-hidden="true">
                <svg class="zy-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $service->iconPath }}" />
                </svg>
            </span>
        @endif
        <p class="zy-card__eyebrow" style="margin-top: var(--zy-space-2);">{{ $service->categoryName }}</p>
        <p class="zy-card__title">{{ $service->title }}</p>
        <p class="zy-card__body">{{ $service->excerpt }}</p>
    </x-ui.card>
</a>
