@props([])

<button
    type="button"
    {{ $attributes->class(['zy-portal-theme-switch']) }}
    x-on:click="dark = !dark"
    x-bind:aria-pressed="dark.toString()"
    x-bind:aria-label="dark ? 'Switch to light mode' : 'Switch to dark mode'"
    title="Toggle theme"
>
    <span class="zy-portal-theme-switch__track" aria-hidden="true">
        <span class="zy-portal-theme-switch__sky zy-portal-theme-switch__sky--day">
            <span class="zy-portal-theme-switch__cloud zy-portal-theme-switch__cloud--a"></span>
            <span class="zy-portal-theme-switch__cloud zy-portal-theme-switch__cloud--b"></span>
        </span>
        <span class="zy-portal-theme-switch__sky zy-portal-theme-switch__sky--night">
            <span class="zy-portal-theme-switch__star zy-portal-theme-switch__star--a"></span>
            <span class="zy-portal-theme-switch__star zy-portal-theme-switch__star--b"></span>
            <span class="zy-portal-theme-switch__star zy-portal-theme-switch__star--c"></span>
        </span>
        <span
            class="zy-portal-theme-switch__knob"
            :class="dark ? 'is-dark' : 'is-light'"
        ></span>
    </span>
    <span class="zy-portal-theme-switch__label zy-portal__foot-label" x-text="dark ? 'Dark' : 'Light'"></span>
</button>
