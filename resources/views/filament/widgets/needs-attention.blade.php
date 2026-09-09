@php
    $items = $this->getItems();
@endphp

<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Needs attention
        </x-slot>

        <x-slot name="description">
            Pending RFQs and overdue invoices
        </x-slot>

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <h3 class="mb-2 text-xs font-semibold tracking-tight text-gray-950 dark:text-white">Pending RFQs</h3>
                @forelse ($items['requests'] as $item)
                    <a href="{{ $item['url'] }}" class="fi-needs-attention-row">
                        <span class="font-medium text-gray-950 dark:text-white">{{ $item['label'] }}</span>
                        <span class="text-[0.6875rem] text-gray-500 dark:text-gray-400">{{ $item['meta'] }}</span>
                    </a>
                @empty
                    <p class="text-xs text-gray-500 dark:text-gray-400">No pending RFQs.</p>
                @endforelse
            </div>

            <div>
                <h3 class="mb-2 text-xs font-semibold tracking-tight text-gray-950 dark:text-white">Overdue invoices</h3>
                @forelse ($items['invoices'] as $item)
                    <a href="{{ $item['url'] }}" class="fi-needs-attention-row">
                        <span class="font-medium text-gray-950 dark:text-white">{{ $item['label'] }}</span>
                        <span class="text-[0.6875rem] text-gray-500 dark:text-gray-400">{{ $item['meta'] }}</span>
                    </a>
                @empty
                    <p class="text-xs text-gray-500 dark:text-gray-400">No overdue invoices.</p>
                @endforelse
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
