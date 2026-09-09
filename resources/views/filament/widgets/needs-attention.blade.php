@php
    $categories = $this->getCategories();
@endphp

<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Needs attention
        </x-slot>

        <x-slot name="description">
            Actionable items requiring follow-up
        </x-slot>

        @if ($categories === [])
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Nothing requires immediate attention.
            </p>
        @else
            <div class="flex flex-col gap-1.5">
                @foreach ($categories as $item)
                    <a href="{{ $item['url'] }}" class="fi-needs-attention-row">
                        <span class="font-medium text-gray-950 dark:text-white">{{ $item['title'] }}</span>
                        <span class="text-[0.6875rem] text-gray-500 dark:text-gray-400">{{ $item['meta'] }}</span>
                    </a>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
