@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
])

@php
    $sizeClass = match ($size) {
        'sm' => 'zy-btn--sm',
        'lg' => 'zy-btn--lg',
        default => null,
    };
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->class([
        'zy-btn',
        "zy-btn--{$variant}",
        $sizeClass,
    ]) }}
>
    {{ $slot }}
</button>
