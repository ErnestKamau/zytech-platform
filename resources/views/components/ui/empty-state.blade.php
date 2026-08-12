@props([
    'title',
    'description' => null,
    'lottie' => null,
])

<div {{ $attributes->class('zy-empty') }}>
    @if ($lottie)
        <div class="zy-empty__media">
            <x-ui.lottie :src="$lottie" class="zy-empty__lottie" />
        </div>
    @elseif (isset($illustration))
        <div class="zy-empty__media">
            {{ $illustration }}
        </div>
    @endif

    <div class="zy-empty__copy">
        <h2 class="zy-empty__title">{{ $title }}</h2>

        @if ($description)
            <p class="zy-empty__description">{{ $description }}</p>
        @endif
    </div>

    @if (isset($actions))
        <div class="zy-empty__actions">
            {{ $actions }}
        </div>
    @elseif (! $slot->isEmpty())
        <div class="zy-empty__actions">
            {{ $slot }}
        </div>
    @endif
</div>
