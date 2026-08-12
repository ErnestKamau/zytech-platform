@props([
    'eyebrow' => null,
    'title' => null,
    'description',
    'icon' => 'inbox',
])

<div {{ $attributes->class('zy-portal-empty') }}>
    <div class="zy-portal-empty__icon" aria-hidden="true">
        <x-portal.icon :name="$icon" size="md" />
    </div>
    @if ($eyebrow)
        <p class="zy-section__eyebrow">{{ $eyebrow }}</p>
    @endif
    @if ($title)
        <p class="zy-portal-empty__title">{{ $title }}</p>
    @endif
    <p>{{ $description }}</p>
    @if ($slot->isNotEmpty())
        <div class="zy-portal-empty__actions">
            {{ $slot }}
        </div>
    @endif
</div>
