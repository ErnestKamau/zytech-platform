<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $quotation->reference_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1c1815; font-size: 12px; }
        h1 { font-size: 22px; margin: 0 0 4px; color: #3b4b31; }
        h2 { font-size: 14px; margin: 18px 0 8px; color: #5c7349; }
        .meta { color: #6b6560; margin-bottom: 18px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border-bottom: 1px solid #ddd5cc; padding: 8px 6px; text-align: left; }
        th { background: #f4f1ec; font-size: 11px; text-transform: uppercase; letter-spacing: .04em; }
        .totals { margin-top: 16px; width: 40%; margin-left: auto; }
        .totals td { border: 0; padding: 4px 0; }
        .totals .grand { font-weight: bold; font-size: 14px; color: #3b4b31; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; background: #e7ecdf; color: #3b4b31; font-size: 10px; }
    </style>
</head>
<body>
    <h1>{{ $quotation->title }}</h1>
    <p class="meta">
        {{ $quotation->reference_number }}
        · <span class="badge">{{ $quotation->status->label() }}</span>
        @if ($quotation->valid_until)
            · Valid until {{ $quotation->valid_until->toFormattedDateString() }}
        @endif
    </p>

    <p>
        <strong>Client:</strong> {{ $quotation->client?->name ?? '—' }}<br>
        <strong>Prepared by:</strong> {{ $quotation->preparer?->name ?? 'Zytech Contractors' }}
    </p>

    @foreach ($quotation->sections as $section)
        <h2>{{ $section->title }}</h2>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($section->items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->label }}</strong>
                            @if ($item->description)
                                <br><span style="color:#6b6560">{{ $item->description }}</span>
                            @endif
                        </td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->unit }}</td>
                        <td>{{ number_format((float) $item->unit_price, 2) }} {{ $quotation->currency }}</td>
                        <td>{{ number_format((float) $item->line_total, 2) }} {{ $quotation->currency }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    @if ($quotation->sections->isEmpty() && $quotation->items->isNotEmpty())
        <h2>Line items</h2>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($quotation->items as $item)
                    <tr>
                        <td>{{ $item->label }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->unit }}</td>
                        <td>{{ number_format((float) $item->unit_price, 2) }}</td>
                        <td>{{ number_format((float) $item->line_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <table class="totals">
        <tr><td>Subtotal</td><td style="text-align:right">{{ number_format((float) $quotation->subtotal, 2) }} {{ $quotation->currency }}</td></tr>
        <tr><td>Tax</td><td style="text-align:right">{{ number_format((float) $quotation->tax_amount, 2) }} {{ $quotation->currency }}</td></tr>
        <tr><td>Discount</td><td style="text-align:right">{{ number_format((float) $quotation->discount_amount, 2) }} {{ $quotation->currency }}</td></tr>
        <tr class="grand"><td>Total</td><td style="text-align:right">{{ number_format((float) $quotation->total_amount, 2) }} {{ $quotation->currency }}</td></tr>
    </table>

    @if ($quotation->notes)
        <h2>Notes</h2>
        <p>{{ $quotation->notes }}</p>
    @endif

    @if ($quotation->terms)
        <h2>Terms</h2>
        <p>{{ $quotation->terms }}</p>
    @endif
</body>
</html>
