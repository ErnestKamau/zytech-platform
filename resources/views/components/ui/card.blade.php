@props([
    'interactive' => false,
    'featured' => false,
])

<div
    {{ $attributes->class([
        'zy-card',
        'zy-card--interactive' => $interactive,
        'zy-card--featured' => $featured,
    ]) }}
>
    {{ $slot }}
</div>
