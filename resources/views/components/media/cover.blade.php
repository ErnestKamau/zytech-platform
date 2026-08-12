@props([
    'src',
    'alt' => '',
])

<div {{ $attributes->class('zy-media zy-media-cover') }}>
    <img src="{{ $src }}" alt="{{ $alt }}" loading="lazy">
</div>
