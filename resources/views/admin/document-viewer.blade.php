<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $reference }} — {{ $docTitle }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root {
            --ink: #1c1815;
            --muted: #6b6560;
            --line: #e4ded5;
            --panel: #faf9f6;
            --accent: #5c7349;
            --accent-dark: #3b4b31;
            --amber: #b45309;
            --red: #b42318;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Instrument Sans', -apple-system, BlinkMacSystemFont, sans-serif; background: #f1efe9; color: var(--ink); }
        a { color: inherit; }
        table { border-collapse: collapse; }

        .topbar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 14px 28px; background: #fff; border-bottom: 1px solid var(--line);
            position: sticky; top: 0; z-index: 10;
        }
        .topbar .crumb { font-size: 13px; color: var(--muted); }
        .topbar .crumb a { text-decoration: none; }
        .topbar .crumb a:hover { text-decoration: underline; }
        .topbar .title-row { display: flex; align-items: center; gap: 10px; margin-top: 2px; }
        .topbar h1 { font-size: 18px; margin: 0; font-weight: 700; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; letter-spacing: .02em; background: #eef2e8; color: var(--accent-dark); border: 1px solid #d8e2cd; }
        .actions { display: flex; gap: 8px; }
        .btn { appearance: none; border: 1px solid var(--line); background: #fff; color: var(--ink); padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
        .btn:hover { background: #f6f4ef; }
        .btn-primary { background: var(--accent-dark); border-color: var(--accent-dark); color: #fff; }
        .btn-primary:hover { background: #2e3b26; }

        .layout { display: grid; grid-template-columns: minmax(0, 1fr) 340px; gap: 24px; max-width: 1280px; margin: 0 auto; padding: 28px; align-items: start; }

        .doc-pane { background: #fff; border: 1px solid var(--line); border-radius: 10px; box-shadow: 0 1px 2px rgba(0,0,0,.04), 0 12px 32px rgba(28,24,21,.06); padding: 40px 48px; }

        /* Shared document-sheet typography (mirrors the PDF) */
        .doc-pane table { width: 100%; }
        .doc-pane .doc-muted { color: var(--muted); }
        .doc-pane .doc-header td { vertical-align: top; }
        .doc-pane .doc-brand { font-size: 22px; font-weight: 700; color: var(--accent-dark); margin: 0 0 4px; }
        .doc-pane .doc-title { font-size: 24px; font-weight: 700; letter-spacing: .08em; color: var(--accent-dark); text-align: right; margin: 0; }
        .doc-pane .doc-meta td { padding: 1px 0; font-size: 12.5px; }
        .doc-pane .doc-meta td.label { color: var(--muted); text-align: right; padding-right: 8px; }
        .doc-pane .doc-meta td.value { text-align: right; width: 1%; white-space: nowrap; font-weight: 600; }
        .doc-pane .doc-draft { display: inline-block; margin-top: 6px; padding: 3px 10px; border: 1px solid var(--amber); color: var(--amber); font-size: 10px; letter-spacing: .1em; border-radius: 4px; }
        .doc-pane .doc-rule { border-top: 2px solid var(--accent); margin: 20px 0; }
        .doc-pane .doc-parties td { vertical-align: top; width: 50%; }
        .doc-pane .doc-eyebrow { font-size: 10px; text-transform: uppercase; letter-spacing: .1em; color: var(--accent); margin: 0 0 4px; font-weight: 700; }
        .doc-pane .doc-subject { font-size: 16px; font-weight: 700; margin: 22px 0 4px; }
        .doc-pane .doc-items { margin-top: 12px; }
        .doc-pane .doc-items th { background: var(--panel); font-size: 10px; text-transform: uppercase; letter-spacing: .06em; color: #4a443e; padding: 9px 8px; text-align: left; border-bottom: 1px solid var(--line); }
        .doc-pane .doc-items td { padding: 9px 8px; border-bottom: 1px solid #ece6df; vertical-align: top; font-size: 13px; }
        .doc-pane .doc-items .num { text-align: right; white-space: nowrap; }
        .doc-pane .doc-items .group td { background: var(--panel); font-weight: 700; color: var(--accent-dark); padding-top: 11px; }
        .doc-pane .doc-optional { font-size: 10px; color: var(--amber); }
        .doc-pane .doc-totals { width: 44%; margin-left: auto; margin-top: 14px; }
        .doc-pane .doc-totals td { padding: 5px 8px; font-size: 13px; }
        .doc-pane .doc-totals td.num { text-align: right; white-space: nowrap; }
        .doc-pane .doc-totals .grand td { border-top: 2px solid var(--accent); font-weight: 700; font-size: 15px; color: var(--accent-dark); padding-top: 10px; }
        .doc-pane .doc-h2 { font-size: 13px; color: var(--accent-dark); margin: 26px 0 8px; text-transform: uppercase; letter-spacing: .06em; }
        .doc-pane .doc-terms { font-size: 12.5px; color: #3d3833; line-height: 1.6; }
        .doc-pane .doc-payment-box { margin-top: 16px; padding: 12px 14px; background: var(--panel); border: 1px solid var(--line); border-radius: 6px; }
        .doc-pane .doc-payment-box td { padding: 2px 0; font-size: 12.5px; }
        .doc-pane .doc-disclaimer { margin-top: 20px; padding: 10px 12px; border: 1px solid var(--line); border-radius: 6px; font-size: 11.5px; color: var(--muted); font-style: italic; }
        .doc-pane .doc-footer { display: none; }

        .sidebar { background: #fff; border: 1px solid var(--line); border-radius: 10px; }
        .tabs { display: flex; border-bottom: 1px solid var(--line); }
        .tabs input { display: none; }
        .tabs label { flex: 1; text-align: center; padding: 12px 4px; font-size: 12px; font-weight: 600; color: var(--muted); cursor: pointer; border-bottom: 2px solid transparent; }
        #tab-timeline:checked ~ .tabs label[for="tab-timeline"],
        #tab-delivery:checked ~ .tabs label[for="tab-delivery"] { color: var(--accent-dark); border-bottom-color: var(--accent-dark); }
        .panel { display: none; padding: 18px 20px; }
        #tab-timeline:checked ~ .panels .panel-timeline,
        #tab-delivery:checked ~ .panels .panel-delivery { display: block; }

        .timeline-item { position: relative; padding-left: 20px; padding-bottom: 16px; border-left: 2px solid var(--line); margin-left: 5px; }
        .timeline-item:last-child { border-color: transparent; padding-bottom: 0; }
        .timeline-item::before { content: ''; position: absolute; left: -6px; top: 2px; width: 10px; height: 10px; border-radius: 50%; background: var(--accent); border: 2px solid #fff; box-shadow: 0 0 0 1px var(--accent); }
        .timeline-item .t-label { font-size: 13px; font-weight: 600; }
        .timeline-item .t-meta { font-size: 11px; color: var(--muted); margin-top: 2px; }
        .empty { color: var(--muted); font-size: 13px; padding: 12px 0; }

        .delivery-row { display: flex; align-items: center; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--line); font-size: 12.5px; }
        .delivery-row:last-child { border-bottom: none; }
        .chip { font-size: 10.5px; font-weight: 700; padding: 2px 8px; border-radius: 999px; }
        .chip-sent { background: #eef2e8; color: var(--accent-dark); }
        .chip-failed { background: #fdecea; color: var(--red); }
        .chip-skipped { background: #f4f1ec; color: var(--muted); }

        @media print {
            .topbar, .sidebar { display: none; }
            .layout { display: block; padding: 0; max-width: none; }
            .doc-pane { box-shadow: none; border: none; }
        }
    </style>
</head>
<body>
    <div class="topbar">
        <div>
            <div class="crumb">
                <a href="{{ url('/admin') }}">Admin</a> ·
                {{ $type === 'quotation' ? 'Quotations' : ($type === 'proforma' ? 'Proforma invoices' : 'Invoices') }}
            </div>
            <div class="title-row">
                <h1>{{ $reference }}</h1>
                <span class="badge">{{ $statusLabel }}</span>
            </div>
        </div>
        <div class="actions">
            <a class="btn" href="{{ $downloadUrl }}" target="_blank" rel="noopener">Download PDF</a>
            <button type="button" class="btn btn-primary" onclick="window.print()">Print</button>
        </div>
    </div>

    <div class="layout">
        <div class="doc-pane">
            @include($sheetView, $sheetData)
        </div>

        <div class="sidebar">
            <input type="radio" name="tabs" id="tab-timeline" checked>
            <input type="radio" name="tabs" id="tab-delivery">

            <div class="tabs">
                <label for="tab-timeline">Timeline</label>
                <label for="tab-delivery">Delivery</label>
            </div>

            <div class="panels">
                <div class="panel panel-timeline">
                    @forelse ($activities as $activity)
                        <div class="timeline-item">
                            <div class="t-label">{{ \App\Domains\Operations\Services\ActivityLogger::labelFor($activity->event) }}</div>
                            <div class="t-meta">{{ $activity->created_at?->format('d M Y, H:i') }} @if ($activity->actor) · {{ $activity->actor->name }} @endif</div>
                        </div>
                    @empty
                        <div class="empty">No activity recorded yet.</div>
                    @endforelse
                </div>

                <div class="panel panel-delivery">
                    @php
                        $sent = $notifications->where('status', \App\Core\Enums\DeliveryStatus::Sent);
                        $sentChannels = $sent->unique('channel')->map(fn ($n) => $n->channel?->label())->filter()->values();
                        $lastSent = $sent->sortByDesc('created_at')->first();
                    @endphp
                    @if ($sentChannels->isNotEmpty())
                        <p style="font-size:13px;margin:0 0 14px;padding:10px 12px;background:var(--panel);border-radius:8px;">
                            Sent via {{ $sentChannels->join(', ', ' and ') }} on {{ $lastSent?->created_at?->format('d M Y') }}.
                        </p>
                    @endif
                    @forelse ($notifications as $notification)
                        <div class="delivery-row">
                            <span>{{ $notification->channel?->label() ?? $notification->channel?->value }} · {{ $notification->created_at?->format('d M Y, H:i') }}</span>
                            <span class="chip chip-{{ $notification->status?->value }}">{{ $notification->status?->value }}</span>
                        </div>
                    @empty
                        <div class="empty">Not sent yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</body>
</html>
