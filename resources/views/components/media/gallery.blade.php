@props([
    'leftSrc',
    'leftAlt' => '',
    'rightSrc',
    'rightAlt' => '',
])

<div {{ $attributes->class('zy-media-gallery') }}>
    <div class="zy-media zy-media-gallery__frame">
        <img src="{{ $leftSrc }}" alt="{{ $leftAlt }}" loading="lazy">
    </div>
    <div class="zy-media-gallery__copy">
        {{ $slot }}
    </div>
    <div class="zy-media zy-media-gallery__frame">
        <img src="{{ $rightSrc }}" alt="{{ $rightAlt }}" loading="lazy">
    </div>
</div>
