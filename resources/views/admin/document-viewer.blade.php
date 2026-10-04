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

        .topbar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 14px 24px; background: #fff; border-bottom: 1px solid var(--line);
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

        .layout { display: grid; grid-template-columns: minmax(0, 1fr) 360px; gap: 0; min-height: calc(100vh - 61px); }

        .canvas-pane { padding: 28px; display: flex; justify-content: center; align-items: flex-start; }
        .sheet { width: 100%; max-width: 760px; background: #fff; box-shadow: 0 1px 2px rgba(0,0,0,.04), 0 12px 32px rgba(28,24,21,.08); border: 1px solid var(--line); border-radius: 6px; overflow: hidden; }
        .sheet-toolbar { display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; background: var(--panel); border-bottom: 1px solid var(--line); font-size: 12px; color: var(--muted); }
        .sheet embed { width: 100%; height: 80vh; display: block; border: none; }

        .sidebar { background: #fff; border-left: 1px solid var(--line); padding: 0; }
        .tabs { display: flex; border-bottom: 1px solid var(--line); }
        .tabs input { display: none; }
        .tabs label { flex: 1; text-align: center; padding: 12px 4px; font-size: 12px; font-weight: 600; color: var(--muted); cursor: pointer; border-bottom: 2px solid transparent; }
        #tab-summary:checked ~ .tabs label[for="tab-summary"],
        #tab-timeline:checked ~ .tabs label[for="tab-timeline"],
        #tab-delivery:checked ~ .tabs label[for="tab-delivery"] { color: var(--accent-dark); border-bottom-color: var(--accent-dark); }
        .panel { display: none; padding: 18px 20px; }
        #tab-summary:checked ~ .panels .panel-summary,
        #tab-timeline:checked ~ .panels .panel-timeline,
        #tab-delivery:checked ~ .panels .panel-delivery { display: block; }

        .kv { display: flex; justify-content: space-between; padding: 7px 0; border-bottom: 1px dashed var(--line); font-size: 13px; }
        .kv:last-child { border-bottom: none; }
        .kv .k { color: var(--muted); }
        .kv .v { font-weight: 600; text-align: right; }
        .totals-box { margin-top: 14px; padding: 12px; background: var(--panel); border-radius: 8px; }
        .totals-box .grand { font-size: 15px; font-weight: 700; color: var(--accent-dark); border-top: 1px solid var(--line); margin-top: 6px; padding-top: 8px; }

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
            <a class="btn" href="{{ $downloadUrl }}" target="_blank" rel="noopener">Download</a>
            <a class="btn" href="{{ $streamUrl }}" target="_blank" rel="noopener">Print</a>
        </div>
    </div>

    <div class="layout">
        <div class="canvas-pane">
            <div class="sheet">
                <div class="sheet-toolbar">
                    <span>{{ $docTitle }} · {{ $reference }}</span>
                    <span>{{ $currency }} {{ number_format((float) $total, 2) }}</span>
                </div>
                <embed src="{{ $streamUrl }}" type="application/pdf">
            </div>
        </div>

        <div class="sidebar">
            <input type="radio" name="tabs" id="tab-summary" checked>
            <input type="radio" name="tabs" id="tab-timeline">
            <input type="radio" name="tabs" id="tab-delivery">

            <div class="tabs">
                <label for="tab-summary">Summary</label>
                <label for="tab-timeline">Timeline</label>
                <label for="tab-delivery">Delivery</label>
            </div>

            <div class="panels">
                <div class="panel panel-summary">
                    <div class="kv"><span class="k">Client</span><span class="v">{{ $clientName ?? '—' }}</span></div>
                    @if ($clientEmail)
                        <div class="kv"><span class="k">Email</span><span class="v">{{ $clientEmail }}</span></div>
                    @endif
                    <div class="kv"><span class="k">Issued</span><span class="v">{{ $issuedDate?->format('d M Y') ?? '—' }}</span></div>
                    @if ($validUntil)
                        <div class="kv"><span class="k">{{ $validUntilLabel }}</span><span class="v">{{ $validUntil->format('d M Y') }}</span></div>
                    @endif

                    <div class="totals-box">
                        <div class="kv"><span class="k">Subtotal</span><span class="v">{{ $currency }} {{ number_format((float) $subtotal, 2) }}</span></div>
                        @if ((float) $discount > 0)
                            <div class="kv"><span class="k">Discount</span><span class="v">− {{ $currency }} {{ number_format((float) $discount, 2) }}</span></div>
                        @endif
                        <div class="kv"><span class="k">Tax</span><span class="v">{{ $currency }} {{ number_format((float) $tax, 2) }}</span></div>
                        <div class="kv grand"><span class="k">Total</span><span class="v">{{ $currency }} {{ number_format((float) $total, 2) }}</span></div>
                    </div>
                </div>

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
