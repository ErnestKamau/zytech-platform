@props([
    'label' => 'Actions',
])

<div
    class="zy-dropdown"
    x-data="{ open: false }"
    x-on:keydown.escape.window="open = false"
    x-on:click.outside="open = false"
>
    <x-ui.button
        type="button"
        variant="secondary"
        class="zy-dropdown__trigger"
        x-on:click="open = !open"
        x-bind:aria-expanded="open.toString()"
    >
        {{ $label }}
        <svg class="zy-icon zy-dropdown__chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
        </svg>
    </x-ui.button>

    <div
        class="zy-dropdown__menu"
        x-show="open"
        x-cloak
        x-transition:enter="zy-transition-fast"
        x-transition:enter-start="zy-transition-scale-start"
        x-transition:enter-end="zy-transition-scale-end"
        x-transition:leave="zy-transition-fast"
        x-transition:leave-start="zy-transition-scale-end"
        x-transition:leave-end="zy-transition-scale-start"
        role="menu"
    >
        {{ $slot }}
    </div>
</div>
