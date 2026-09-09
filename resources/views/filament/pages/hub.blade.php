<x-filament-panels::page>
    @if ($subheading = $this->getSubheading())
        <p class="mb-4 max-w-2xl text-xs leading-relaxed text-gray-500 dark:text-gray-400 sm:mb-5">
            {{ $subheading }}
        </p>
    @endif

    <div class="space-y-6 sm:space-y-8">
        @foreach ($this->getHubSections() as $section)
            <section>
                <div class="mb-3">
                    <h2 class="text-sm font-semibold tracking-tight text-gray-950 dark:text-white">
                        {{ $section['title'] }}
                    </h2>
                    @if (! empty($section['description']))
                        <p class="mt-0.5 max-w-2xl text-xs leading-relaxed text-gray-500 dark:text-gray-400">
                            {{ $section['description'] }}
                        </p>
                    @endif
                </div>

                <div class="grid gap-2.5 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($section['cards'] as $card)
                        <a
                            href="{{ $card['url'] }}"
                            class="fi-hub-card group flex min-h-14 items-start gap-3 rounded-xl border border-gray-200 bg-white p-3.5 transition hover:border-primary-400 hover:bg-primary-50/40 dark:border-white/10 dark:bg-white/[0.04] dark:hover:border-primary-400/60 dark:hover:bg-primary-400/5"
                        >
                            <span class="fi-hub-card-icon flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-700 transition group-hover:scale-105 group-hover:bg-primary-100 group-hover:text-primary-700 dark:bg-white/10 dark:text-gray-200 dark:group-hover:bg-primary-400/20 dark:group-hover:text-primary-300">
                                <x-filament::icon
                                    :icon="$card['icon']"
                                    class="h-4 w-4"
                                />
                            </span>

                            <span class="min-w-0 flex-1 pt-0.5">
                                <span class="flex items-start justify-between gap-2">
                                    <span class="text-xs font-semibold tracking-tight text-gray-950 dark:text-white">
                                        {{ $card['label'] }}
                                    </span>
                                    @if (isset($card['count']) && $card['count'] !== null)
                                        <span class="rounded-full bg-gray-100 px-1.5 py-0.5 text-[0.6875rem] font-medium tabular-nums text-gray-600 dark:bg-white/10 dark:text-gray-300">
                                            {{ $card['count'] }}
                                        </span>
                                    @endif
                                </span>
                                @if (! empty($card['description']))
                                    <span class="mt-0.5 block text-[0.75rem] leading-snug text-gray-500 dark:text-gray-400">
                                        {{ $card['description'] }}
                                    </span>
                                @endif
                            </span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>
</x-filament-panels::page>
