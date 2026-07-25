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
        x-on:click="open = !open"
        x-bind:aria-expanded="open.toString()"
    >
        {{ $label }}
    </x-ui.button>

    <div
        class="zy-dropdown__menu"
        x-show="open"
        x-cloak
        x-transition
        role="menu"
    >
        {{ $slot }}
    </div>
</div>
