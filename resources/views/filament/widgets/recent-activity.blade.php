@php
    $items = $this->getItems();
@endphp

<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Recent activity
        </x-slot>

        <x-slot name="description">
            Business timeline from domain activity
        </x-slot>

        @if ($items === [])
            <p class="text-sm text-gray-500 dark:text-gray-400">
                No recent activity yet.
            </p>
        @else
            <div class="flex flex-col gap-1.5">
                @foreach ($items as $item)
                    @if ($item['url'])
                        <a href="{{ $item['url'] }}" class="fi-needs-attention-row">
                            <span class="font-medium text-gray-950 dark:text-white">
                                {{ $item['actor'] }} · {{ $item['label'] }}
                            </span>
                            <span class="text-[0.6875rem] text-gray-500 dark:text-gray-400">{{ $item['when'] }}</span>
                        </a>
                    @else
                        <div class="fi-needs-attention-row">
                            <span class="font-medium text-gray-950 dark:text-white">
                                {{ $item['actor'] }} · {{ $item['label'] }}
                            </span>
                            <span class="text-[0.6875rem] text-gray-500 dark:text-gray-400">{{ $item['when'] }}</span>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
