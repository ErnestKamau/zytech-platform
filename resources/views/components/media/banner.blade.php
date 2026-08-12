@props([
    'src' => null,
    'video' => null,
    'poster' => null,
    'alt' => '',
])

<section {{ $attributes->class('zy-media-banner') }}>
    <div class="zy-media-banner__media" aria-hidden="{{ $alt === '' ? 'true' : 'false' }}">
        @if ($video)
            <video muted autoplay loop playsinline preload="metadata" poster="{{ $poster }}">
                <source src="{{ $video }}" type="video/mp4">
            </video>
            @if ($poster)
                <img src="{{ $poster }}" alt="{{ $alt }}" class="zy-media-banner__fallback">
            @endif
        @elseif ($src)
            <img src="{{ $src }}" alt="{{ $alt }}" loading="lazy">
        @endif
    </div>
    <div class="zy-media-banner__scrim"></div>
    <div class="zy-media-banner__content">
        {{ $slot }}
    </div>
</section>
