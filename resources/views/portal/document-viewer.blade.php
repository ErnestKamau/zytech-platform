<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $reference }} — {{ $docTitle }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root {
            --ink: #1c1815; --muted: #6b6560; --line: #e4ded5; --panel: #faf9f6;
            --accent: #5c7349; --accent-dark: #3b4b31;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f1efe9; color: var(--ink); }
        a { color: inherit; }
        .topbar { display: flex; align-items: center; justify-content: space-between; padding: 14px 24px; background: #fff; border-bottom: 1px solid var(--line); position: sticky; top: 0; }
        .crumb { font-size: 13px; color: var(--muted); text-decoration: none; }
        .crumb:hover { text-decoration: underline; }
        .title-row { display: flex; align-items: center; gap: 10px; margin-top: 2px; }
        h1 { font-size: 18px; margin: 0; font-weight: 700; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; background: #eef2e8; color: var(--accent-dark); border: 1px solid #d8e2cd; }
        .actions { display: flex; gap: 8px; }
        .btn { appearance: none; border: 1px solid var(--line); background: #fff; color: var(--ink); padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
        .btn:hover { background: #f6f4ef; }
        .btn-primary { background: var(--accent-dark); border-color: var(--accent-dark); color: #fff; }
        .canvas-pane { padding: 28px; display: flex; justify-content: center; }
        .sheet { width: 100%; max-width: 820px; background: #fff; box-shadow: 0 1px 2px rgba(0,0,0,.04), 0 12px 32px rgba(28,24,21,.08); border: 1px solid var(--line); border-radius: 6px; overflow: hidden; }
        .sheet-toolbar { display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; background: var(--panel); border-bottom: 1px solid var(--line); font-size: 12px; color: var(--muted); }
        .sheet embed { width: 100%; height: 82vh; display: block; border: none; }
    </style>
</head>
<body>
    <div class="topbar">
        <div>
            <a class="crumb" href="{{ $backUrl }}">&larr; {{ $backLabel }}</a>
            <div class="title-row">
                <h1>{{ $reference }}</h1>
                <span class="badge">{{ $statusLabel }}</span>
            </div>
        </div>
        <div class="actions">
            <a class="btn" href="{{ $downloadUrl }}">Download</a>
            <a class="btn btn-primary" href="{{ $streamUrl }}" target="_blank" rel="noopener">Open in new tab</a>
        </div>
    </div>

    <div class="canvas-pane">
        <div class="sheet">
            <div class="sheet-toolbar">
                <span>{{ $docTitle }} · {{ $reference }}</span>
                <span>{{ $currency }} {{ number_format((float) $total, 2) }}</span>
            </div>
            <embed src="{{ $streamUrl }}" type="application/pdf">
        </div>
    </div>
</body>
</html>
