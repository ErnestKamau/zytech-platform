@props([
    'show' => 'open',
    'title' => null,
])

<template x-teleport="body">
    <div
        class="zy-modal"
        x-show="{{ $show }}"
        x-cloak
        x-on:keydown.escape.window="{{ $show }} = false"
        role="dialog"
        aria-modal="true"
        @if ($title) aria-label="{{ $title }}" @endif
    >
        <div class="zy-modal__backdrop" x-on:click="{{ $show }} = false"></div>

        <div class="zy-modal__panel" x-on:click.stop>
            <div class="zy-modal__header">
                @if ($title)
                    <h3 class="zy-modal__title">{{ $title }}</h3>
                @else
                    <span></span>
                @endif
                <button
                    type="button"
                    class="zy-modal__close"
                    x-on:click="{{ $show }} = false"
                    aria-label="Close"
                >
                    <svg class="zy-icon zy-icon--sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="zy-modal__body">
                {{ $slot }}
            </div>

            @isset($footer)
                <div class="zy-modal__footer">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</template>
