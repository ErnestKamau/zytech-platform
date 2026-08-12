@props([
    'searchModel' => 'search',
    'filterModel' => null,
    'filterOptions' => [],
    'filterLabel' => 'Filter',
    'placeholder' => 'Search…',
    'exportAction' => null,
])

<div {{ $attributes->class('zy-portal-toolbar') }}>
    <div class="zy-portal-toolbar__search">
        <span class="zy-portal-toolbar__search-icon" aria-hidden="true">
            <x-portal.icon name="search" />
        </span>
        <label class="zy-sr-only" for="zy-portal-search">{{ $placeholder }}</label>
        <input
            id="zy-portal-search"
            type="search"
            class="zy-input"
            placeholder="{{ $placeholder }}"
            wire:model.live.debounce.300ms="{{ $searchModel }}"
        >
    </div>

    @if ($filterModel !== null)
        <div class="zy-portal-toolbar__filters">
            <label class="zy-sr-only" for="zy-portal-filter">{{ $filterLabel }}</label>
            <select id="zy-portal-filter" class="zy-input" wire:model.live="{{ $filterModel }}" aria-label="{{ $filterLabel }}">
                @foreach ($filterOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
    @endif

    <div class="zy-portal-toolbar__actions">
        @if ($exportAction)
            <button type="button" class="zy-btn zy-btn--secondary zy-btn--sm" wire:click="{{ $exportAction }}">
                <x-portal.icon name="download" />
                Export Excel
            </button>
        @endif
        {{ $slot }}
    </div>
</div>
