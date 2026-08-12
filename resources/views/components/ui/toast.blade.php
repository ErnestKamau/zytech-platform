@props([
    'type' => 'info',
    'title' => null,
])

<div {{ $attributes->class(['zy-toast', "zy-toast--{$type}"]) }} role="status">
    @if ($title)
        <p class="zy-toast__title">{{ $title }}</p>
    @endif
    <div class="zy-toast__body">{{ $slot }}</div>
</div>
