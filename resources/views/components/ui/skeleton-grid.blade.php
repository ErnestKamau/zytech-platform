@props([
    'count' => 3,
    'variant' => 'card',
    'lines' => 3,
])

@php
    $items = max(1, (int) $count);
@endphp

<div {{ $attributes->class('zy-grid zy-grid--3') }} aria-hidden="true" aria-busy="true">
    @for ($i = 0; $i < $items; $i++)
        <x-ui.skeleton :variant="$variant" :lines="$lines" />
    @endfor
</div>
