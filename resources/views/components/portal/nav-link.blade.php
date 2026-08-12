@props([
    'href',
    'label',
    'icon' => 'home',
    'active' => false,
    'badge' => null,
])

<a
    href="{{ $href }}"
    aria-label="{{ $label }}"
    data-tooltip="{{ $label }}"
    @class(['is-active' => $active])
    @if ($active) aria-current="page" @endif
    {{ $attributes }}
>
    <span class="zy-portal__link-icon" aria-hidden="true">
        <x-portal.icon :name="$icon" />
    </span>
    <span class="zy-portal__link-label">{{ $label }}</span>
    @if ($badge !== null && (int) $badge > 0)
        <span class="zy-portal__link-badge">{{ (int) $badge > 99 ? '99+' : (int) $badge }}</span>
    @endif
</a>
