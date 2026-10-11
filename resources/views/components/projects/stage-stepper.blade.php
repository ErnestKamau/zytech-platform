@props([
    'stage',
    'compact' => false,
])

@php
    $stages = \App\Core\Enums\ConstructionStage::cases();
    $currentIndex = array_search($stage, $stages, true);
    $currentIndex = $currentIndex === false ? 0 : $currentIndex;
@endphp

<ol {{ $attributes->class(['zy-stage-stepper', 'zy-stage-stepper--compact' => $compact]) }}>
    @foreach ($stages as $index => $step)
        <li class="{{ $index < $currentIndex ? 'is-done' : ($index === $currentIndex ? 'is-current' : '') }}">
            <span class="zy-stage-stepper__dot" aria-hidden="true"></span>
            <span class="zy-stage-stepper__label">{{ $step->label() }}</span>
        </li>
    @endforeach
</ol>
