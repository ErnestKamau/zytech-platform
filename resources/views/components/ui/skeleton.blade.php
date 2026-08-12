@props([
    'variant' => 'line',
    'lines' => 3,
])

@php
    $lineCount = max(1, (int) $lines);
@endphp

@if ($variant === 'card')
    <div {{ $attributes->class('zy-skeleton-card') }} aria-hidden="true">
        <div class="zy-skeleton zy-skeleton--media"></div>
        @for ($i = 0; $i < $lineCount; $i++)
            <div
                class="zy-skeleton zy-skeleton--line"
                style="width: {{ $i === $lineCount - 1 ? '62%' : '100%' }}"
            ></div>
        @endfor
    </div>
@elseif ($variant === 'avatar')
    <div {{ $attributes->class('zy-skeleton zy-skeleton--avatar') }} aria-hidden="true"></div>
@elseif ($variant === 'media')
    <div {{ $attributes->class('zy-skeleton zy-skeleton--media') }} aria-hidden="true"></div>
@elseif ($variant === 'block')
    <div {{ $attributes->class('zy-skeleton zy-skeleton--block') }} aria-hidden="true"></div>
@else
    <div {{ $attributes->class('zy-skeleton-stack') }} aria-hidden="true">
        @for ($i = 0; $i < $lineCount; $i++)
            <div
                class="zy-skeleton zy-skeleton--line"
                style="width: {{ $i === $lineCount - 1 ? '55%' : '100%' }}"
            ></div>
        @endfor
    </div>
@endif
