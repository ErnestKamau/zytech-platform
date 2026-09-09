@php
    $funnel = $this->getFunnel();
    $stages = $funnel['stages'];
    $conversions = array_values($funnel['conversions']);
    $periodDays = $funnel['period_days'] ?? 30;
@endphp

<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Sales pipeline
        </x-slot>

        <x-slot name="description">
            Count and value by stage · conversions from last {{ $periodDays }} days (relationship-based)
        </x-slot>

        <div class="fi-sales-funnel flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-stretch sm:gap-2.5">
            @foreach ($stages as $index => $stage)
                <div class="fi-sales-funnel-stage w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 dark:border-white/10 dark:bg-white/[0.04] sm:min-w-[6.5rem] sm:flex-1">
                    <p class="text-[0.6875rem] font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        {{ $stage['label'] }}
                    </p>
                    <p class="mt-0.5 text-lg font-semibold tracking-tight tabular-nums text-gray-950 dark:text-white sm:text-xl">
                        {{ number_format($stage['count']) }}
                    </p>
                    <p class="mt-0.5 text-[0.6875rem] tabular-nums text-gray-500 dark:text-gray-400">
                        @if ($stage['value'] === null)
                            —
                        @else
                            {{ \App\Support\Helpers\MoneyFormatter::compact($stage['value']) }}
                        @endif
                    </p>
                </div>

                @if ($index < count($stages) - 1)
                    <div class="fi-sales-funnel-conversion flex flex-row items-center justify-start gap-1.5 px-1 py-0.5 text-left sm:min-w-[3.25rem] sm:flex-col sm:items-center sm:justify-center sm:px-0 sm:text-center">
                        <span class="text-xs font-semibold tabular-nums text-primary-600 dark:text-primary-400">
                            {{ number_format($conversions[$index] ?? 0, 1) }}%
                        </span>
                        <span class="text-[0.625rem] uppercase tracking-wide text-gray-400">conv.</span>
                    </div>
                @endif
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
