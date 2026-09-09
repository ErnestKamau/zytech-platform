<div class="space-y-6 text-sm">
    <section>
        <h3 class="mb-2 font-semibold text-gray-950 dark:text-white">Activity timeline</h3>
        @if ($activities->isEmpty())
            <p class="text-gray-500 dark:text-gray-400">No business activity recorded yet.</p>
        @else
            <ul class="space-y-3">
                @foreach ($activities as $activity)
                    <li class="border-l-2 border-amber-500 pl-3">
                        <div class="font-medium text-gray-950 dark:text-white">{{ $activity->event }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $activity->created_at?->format('d M Y H:i') }}
                            · {{ $activity->actor?->name ?? 'System' }}
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    <section>
        <h3 class="mb-2 font-semibold text-gray-950 dark:text-white">Notification delivery</h3>
        @if ($notifications->isEmpty())
            <p class="text-gray-500 dark:text-gray-400">No notification attempts logged for this record.</p>
        @else
            <ul class="space-y-3">
                @foreach ($notifications as $notification)
                    <li class="rounded border border-gray-200 p-3 dark:border-gray-700">
                        <div class="font-medium text-gray-950 dark:text-white">
                            {{ $notification->type }}
                            · {{ $notification->channel?->value ?? '—' }}
                            · {{ $notification->status?->value ?? '—' }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $notification->recipient }}
                            @if ($notification->sent_at)
                                · {{ $notification->sent_at->format('d M Y H:i') }}
                            @endif
                        </div>
                        @if ($notification->error)
                            <div class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $notification->error }}</div>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
</div>
