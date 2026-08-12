@props([
    'video' => null,
    'poster' => null,
    'image' => null,
    'alt' => '',
])

<div {{ $attributes->class('zy-media zy-media-hero') }} aria-hidden="{{ $alt === '' ? 'true' : 'false' }}">
    @if ($video)
        <video
            muted
            autoplay
            loop
            playsinline
            preload="metadata"
            poster="{{ $poster }}"
        >
            <source src="{{ $video }}" type="video/mp4">
        </video>
        @if ($poster)
            <img src="{{ $poster }}" alt="{{ $alt }}" class="zy-media-hero__fallback">
        @endif
    @elseif ($image)
        <img src="{{ $image }}" alt="{{ $alt }}" loading="eager">
    @endif
    <div class="zy-media-hero__scrim"></div>
</div>
