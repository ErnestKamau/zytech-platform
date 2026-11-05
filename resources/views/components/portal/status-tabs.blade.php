@props([
    'tabs' => [], // key => ['label' => string, 'count' => int, 'variant' => neutral|info|warning|success|danger]
    'active' => null,
    'model' => 'statusTab',
])

<div {{ $attributes->class('zy-status-tabs') }} role="tablist">
    @foreach ($tabs as $key => $tab)
        @php $variant = $tab['variant'] ?? 'neutral'; @endphp
        <button
            type="button"
            role="tab"
            class="zy-status-tab zy-status-tab--{{ $variant }}"
            aria-selected="{{ $key === $active ? 'true' : 'false' }}"
            wire:click="$set('{{ $model }}', '{{ $key }}')"
        >
            <span>{{ $tab['label'] }}</span>
            <span class="zy-status-tab__count">{{ $tab['count'] ?? 0 }}</span>
        </button>
    @endforeach
</div>
