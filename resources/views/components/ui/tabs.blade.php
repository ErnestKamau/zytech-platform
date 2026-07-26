@props([
    'tabs' => [],
    'default' => null,
])

@php
    $tabKeys = array_keys($tabs);
    $active = $default ?? ($tabKeys[0] ?? null);
@endphp

<div
    class="zy-tabs"
    x-data="{ active: @js($active) }"
    {{ $attributes }}
>
    <div class="zy-tabs__list" role="tablist">
        @foreach ($tabs as $key => $label)
            <button
                type="button"
                class="zy-tabs__tab"
                role="tab"
                id="tab-{{ $key }}"
                aria-selected="{{ $key === $active ? 'true' : 'false' }}"
                x-bind:aria-selected="active === @js($key) ? 'true' : 'false'"
                x-on:click="active = @js($key)"
            >
                {{ $label }}
            </button>
        @endforeach
    </div>

    @foreach ($tabs as $key => $label)
        @php
            $panel = $$key ?? null;
        @endphp
        <div
            class="zy-tabs__panel"
            role="tabpanel"
            aria-labelledby="tab-{{ $key }}"
            x-show="active === @js($key)"
            @if ($key !== $active) x-cloak @endif
            x-transition:enter="zy-transition-fast"
            x-transition:enter-start="zy-transition-fade-start"
            x-transition:enter-end="zy-transition-fade-end"
        >
            {{ $panel }}
        </div>
    @endforeach
</div>
