@props([
    'type' => 'info',
    'title' => null,
])

<div {{ $attributes->class(['zy-alert', "zy-alert--{$type}"]) }} role="alert">
    @if ($title)
        <p class="zy-alert__title">{{ $title }}</p>
    @endif
    <div class="zy-alert__body">{{ $slot }}</div>
</div>
