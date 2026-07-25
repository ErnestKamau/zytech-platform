@props([
    'variant' => 'neutral',
])

<span {{ $attributes->class(['zy-badge', "zy-badge--{$variant}"]) }}>
    {{ $slot }}
</span>
