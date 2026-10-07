<x-filament-panels::page>
    <div class="fi-section rounded-xl border border-gray-200 bg-white p-4 dark:border-white/10 dark:bg-white/5">
        <div class="flex flex-wrap items-center gap-4 text-xs font-medium">
            <span class="inline-flex items-center gap-1.5">
                <span class="inline-block size-2.5 rounded-full" style="background:#5c7349"></span> Invoice due
            </span>
            <span class="inline-flex items-center gap-1.5">
                <span class="inline-block size-2.5 rounded-full" style="background:#b45309"></span> Due soon
            </span>
            <span class="inline-flex items-center gap-1.5">
                <span class="inline-block size-2.5 rounded-full" style="background:#b42318"></span> Overdue
            </span>
            <span class="inline-flex items-center gap-1.5">
                <span class="inline-block size-2.5 rounded-full" style="background:#2ab0df"></span> Proforma expiry
            </span>
            <span class="inline-flex items-center gap-1.5">
                <span class="inline-block size-2.5 rounded-full" style="background:#8c7a68"></span> Site visit
            </span>
        </div>
    </div>

    @livewire(\App\Filament\Widgets\BillingCalendarWidget::class)
</x-filament-panels::page>
