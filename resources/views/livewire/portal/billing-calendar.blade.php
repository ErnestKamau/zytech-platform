<div class="zy-portal-page">
    <x-portal.page-header
        eyebrow="Billing"
        title="Billing calendar"
        lead="Upcoming invoice due dates and proforma invoice validity windows."
        icon="calendar"
    />

    <div class="zy-portal-panel">
        <div class="zy-portal-panel__header">
            <div class="zy-portal-actions">
                <button type="button" class="zy-btn zy-btn--ghost zy-btn--sm" wire:click="previousMonth">&larr;</button>
                <h2 class="zy-portal-panel__title" style="min-width: 10rem; text-align: center;">{{ $cursor->format('F Y') }}</h2>
                <button type="button" class="zy-btn zy-btn--ghost zy-btn--sm" wire:click="nextMonth">&rarr;</button>
            </div>
            <div class="zy-portal-actions">
                <span class="zy-muted"><span style="display:inline-block;width:.6rem;height:.6rem;border-radius:999px;background:#5c7349;margin-right:.3rem;"></span>Invoice due</span>
                <span class="zy-muted"><span style="display:inline-block;width:.6rem;height:.6rem;border-radius:999px;background:#2ab0df;margin-right:.3rem;"></span>Proforma expiry</span>
            </div>
        </div>

        <div style="display:grid; grid-template-columns: repeat(7, 1fr); gap: var(--zy-space-2); margin-top: var(--zy-space-3);">
            @foreach (['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $label)
                <div class="zy-muted" style="text-align:center; font-size: var(--zy-text-xs); font-weight: 600; text-transform: uppercase;">{{ $label }}</div>
            @endforeach

            @foreach ($weeks as $week)
                @foreach ($week as $day)
                    <div style="min-height: 5.5rem; padding: var(--zy-space-2); border-radius: 0.75rem; border: 1px solid var(--zy-color-border); background: {{ $day['inMonth'] ? 'var(--zy-color-surface)' : 'transparent' }}; opacity: {{ $day['inMonth'] ? 1 : 0.4 }};">
                        <p class="zy-muted" style="margin: 0 0 var(--zy-space-1); font-size: var(--zy-text-xs);">{{ $day['date']->day }}</p>
                        @foreach ($day['events'] as $event)
                            <a href="{{ $event['url'] }}" target="_blank" rel="noopener" style="display:block; margin-bottom: 2px; padding: 2px 6px; border-radius: 999px; font-size: 10px; font-weight: 600; text-decoration: none; color: #fff; background: {{ $event['type'] === 'invoice' ? '#5c7349' : '#2ab0df' }};">
                                {{ $event['label'] }}
                            </a>
                        @endforeach
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>
</div>
