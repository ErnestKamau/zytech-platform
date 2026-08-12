@props([
    'eyebrow' => null,
    'title',
    'lead' => null,
    'icon' => null,
])

<header {{ $attributes->class('zy-portal-page__header') }}>
    <div class="zy-portal-page__intro">
        @if ($eyebrow)
            <p class="zy-section__eyebrow">
                @if ($icon)
                    <x-portal.icon :name="$icon" class="zy-portal-page__eyebrow-icon" />
                @endif
                {{ $eyebrow }}
            </p>
        @endif
        <h1 class="zy-portal-page__title">{{ $title }}</h1>
        @if ($lead)
            <p class="zy-portal-page__lead">{{ $lead }}</p>
        @endif
    </div>
    @if ($slot->isNotEmpty())
        <div class="zy-portal-page__actions">
            {{ $slot }}
        </div>
    @endif
</header>
