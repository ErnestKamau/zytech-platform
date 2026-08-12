@props([
    'src',
    'loop' => true,
    'autoplay' => true,
])

@php
    $resolvedSrc = str_starts_with((string) $src, 'http') || str_starts_with((string) $src, '/')
        ? $src
        : asset($src);
@endphp

<canvas
    {{ $attributes->class('zy-lottie') }}
    data-zy-lottie
    data-src="{{ $resolvedSrc }}"
    data-loop="{{ $loop ? '1' : '0' }}"
    data-autoplay="{{ $autoplay ? '1' : '0' }}"
    role="img"
    aria-hidden="true"
></canvas>
