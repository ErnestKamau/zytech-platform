@props([
    'steps' => [],
    'current' => 1,
])

@php
    $currentIndex = max(1, (int) $current);
@endphp

<nav class="zy-wizard" aria-label="Progress">
    <ol class="zy-wizard__steps">
        @foreach ($steps as $index => $label)
            @php
                $stepNumber = $index + 1;
                $state = $stepNumber < $currentIndex
                    ? 'done'
                    : ($stepNumber === $currentIndex ? 'current' : 'upcoming');
            @endphp
            <li class="zy-wizard__step zy-wizard__step--{{ $state }}">
                <span class="zy-wizard__index">{{ $stepNumber }}</span>
                <span class="zy-wizard__label">{{ $label }}</span>
            </li>
        @endforeach
    </ol>
</nav>
