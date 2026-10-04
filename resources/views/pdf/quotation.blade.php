@php
    use App\Core\Enums\QuotationStatus;

    $currency = $quotation->currency ?: 'KES';
    $money = fn ($value): string => number_format((float) $value, 2);
    $request = $quotation->request;
    $clientName = $quotation->client?->name ?? $request?->full_name;
    $clientEmail = $quotation->client?->email ?? $request?->email;
    $clientPhone = $quotation->client?->phone ?? $request?->phone;
    $siteLocation = collect([$request?->location, $request?->county])->filter()->implode(', ');
    $unsectioned = $quotation->items->whereNull('quotation_section_id');
    $groups = $quotation->sections
        ->map(fn ($section) => ['title' => $section->title, 'description' => $section->description, 'items' => $section->items])
        ->filter(fn (array $group) => $group['items']->isNotEmpty())
        ->values();

    if ($unsectioned->isNotEmpty()) {
        $groups->push(['title' => $groups->isEmpty() ? null : 'Other items', 'description' => null, 'items' => $unsectioned]);
    }

    $row = 0;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $quotation->reference_number }}</title>
    <style>
        @page { margin: 32px 36px 48px; }
        body { font-family: DejaVu Sans, sans-serif; color: #1c1815; font-size: 11px; line-height: 1.45; }
        table { width: 100%; border-collapse: collapse; }
        .muted { color: #6b6560; }
        .header td { vertical-align: top; }
        .brand { font-size: 20px; font-weight: bold; color: #3b4b31; margin: 0 0 4px; }
        .doc-title { font-size: 22px; font-weight: bold; letter-spacing: .08em; color: #3b4b31; text-align: right; margin: 0; }
        .doc-meta td { padding: 1px 0; font-size: 10.5px; }
        .doc-meta td.label { color: #6b6560; text-align: right; padding-right: 8px; }
        .doc-meta td.value { text-align: right; width: 1%; white-space: nowrap; }
        .draft { display: inline-block; margin-top: 4px; padding: 2px 8px; border: 1px solid #b45309; color: #b45309; font-size: 9px; letter-spacing: .1em; }
        .rule { border-top: 2px solid #5c7349; margin: 16px 0; }
        .parties td { vertical-align: top; width: 50%; }
        .eyebrow { font-size: 9px; text-transform: uppercase; letter-spacing: .1em; color: #5c7349; margin: 0 0 4px; font-weight: bold; }
        .subject { font-size: 14px; font-weight: bold; margin: 18px 0 2px; }
        .items { margin-top: 10px; }
        .items th { background: #f4f1ec; font-size: 9px; text-transform: uppercase; letter-spacing: .06em; color: #4a443e; padding: 7px 6px; text-align: left; border-bottom: 1px solid #ddd5cc; }
        .items td { padding: 7px 6px; border-bottom: 1px solid #ece6df; vertical-align: top; }
        .items .num { text-align: right; white-space: nowrap; }
        .items .group td { background: #fafaf7; font-weight: bold; color: #3b4b31; padding-top: 9px; }
        .optional { font-size: 9px; color: #b45309; }
        .totals { width: 46%; margin-left: auto; margin-top: 12px; }
        .totals td { padding: 4px 6px; }
        .totals td.num { text-align: right; white-space: nowrap; }
        .totals .grand td { border-top: 2px solid #5c7349; font-weight: bold; font-size: 13px; color: #3b4b31; padding-top: 8px; }
        h2 { font-size: 12px; color: #3b4b31; margin: 22px 0 6px; text-transform: uppercase; letter-spacing: .06em; }
        .terms { font-size: 10px; color: #3d3833; }
        .footer { position: fixed; bottom: -28px; left: 0; right: 0; font-size: 9px; color: #8a837c; text-align: center; }
    </style>
</head>
<body>
    <div class="footer">
        {{ $company?->name ?? 'Zytech Contractors' }} · {{ $quotation->reference_number }}
        @if ($company?->website) · {{ $company->website }} @endif
    </div>

    <table class="header">
        <tr>
            <td style="width: 55%">
                <p class="brand">{{ $company?->name ?? 'Zytech Contractors' }}</p>
                <div class="muted">
                    @if ($company?->location) {{ $company->location }}<br> @endif
                    @if ($company?->phone) {{ $company->phone }} @endif
                    @if ($company?->phone && $company?->email) · @endif
                    @if ($company?->email) {{ $company->email }} @endif
                    @if ($company?->tax_number) <br>PIN: {{ $company->tax_number }} @endif
                </div>
            </td>
            <td style="width: 45%">
                <p class="doc-title">QUOTATION</p>
                <table class="doc-meta">
                    <tr><td class="label">Reference</td><td class="value">{{ $quotation->reference_number }}</td></tr>
                    <tr><td class="label">Date</td><td class="value">{{ ($quotation->sent_at ?? $quotation->created_at ?? now())->format('d M Y') }}</td></tr>
                    @if ($quotation->valid_until)
                        <tr><td class="label">Valid until</td><td class="value">{{ $quotation->valid_until->format('d M Y') }}</td></tr>
                    @endif
                    @if ($request)
                        <tr><td class="label">Request</td><td class="value">{{ $request->reference_number }}</td></tr>
                    @endif
                </table>
                @if (in_array($quotation->status, [QuotationStatus::Draft, QuotationStatus::Reviewing, QuotationStatus::Preparing], true))
                    <div style="text-align: right"><span class="draft">DRAFT — NOT YET ISSUED</span></div>
                @endif
            </td>
        </tr>
    </table>

    <div class="rule"></div>

    <table class="parties">
        <tr>
            <td>
                <p class="eyebrow">Prepared for</p>
                <strong>{{ $clientName ?? '—' }}</strong><br>
                @if ($clientEmail) {{ $clientEmail }}<br> @endif
                @if ($clientPhone) {{ $clientPhone }}<br> @endif
            </td>
            <td>
                @if ($siteLocation !== '' || $request?->project_type)
                    <p class="eyebrow">Project</p>
                    @if ($request?->project_type) {{ $request->project_type->label() }}<br> @endif
                    @if ($siteLocation !== '') {{ $siteLocation }} @endif
                @endif
            </td>
        </tr>
    </table>

    <p class="subject">{{ $quotation->title }}</p>

    <table class="items">
        <thead>
            <tr>
                <th style="width: 4%">#</th>
                <th>Description</th>
                <th class="num" style="width: 9%">Qty</th>
                <th style="width: 9%">Unit</th>
                <th class="num" style="width: 16%">Unit price</th>
                <th class="num" style="width: 17%">Amount ({{ $currency }})</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($groups as $group)
                @if ($group['title'])
                    <tr class="group"><td colspan="6">{{ $group['title'] }}</td></tr>
                @endif
                @foreach ($group['items'] as $item)
                    <tr>
                        <td class="muted">{{ ++$row }}</td>
                        <td>
                            <strong>{{ $item->label }}</strong>
                            @if ($item->is_optional) <span class="optional">(optional — not included in total)</span> @endif
                            @if ($item->description && $item->description !== $item->label)
                                <br><span class="muted">{{ $item->description }}</span>
                            @endif
                        </td>
                        <td class="num">{{ rtrim(rtrim(number_format((float) $item->quantity, 2), '0'), '.') }}</td>
                        <td>{{ $item->unit }}</td>
                        <td class="num">{{ $money($item->unit_price) }}</td>
                        <td class="num">{{ $money($item->line_total) }}</td>
                    </tr>
                @endforeach
            @empty
                <tr><td colspan="6" class="muted" style="text-align: center; padding: 18px">No line items yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    <table class="totals">
        <tr><td>Subtotal</td><td class="num">{{ $currency }} {{ $money($quotation->subtotal) }}</td></tr>
        @if ((float) $quotation->discount_amount > 0)
            <tr><td>Discount</td><td class="num">− {{ $currency }} {{ $money($quotation->discount_amount) }}</td></tr>
        @endif
        <tr><td>VAT ({{ rtrim(rtrim(number_format((float) $quotation->tax_rate, 2), '0'), '.') }}%)</td><td class="num">{{ $currency }} {{ $money($quotation->tax_amount) }}</td></tr>
        <tr class="grand"><td>Total</td><td class="num">{{ $currency }} {{ $money($quotation->total_amount) }}</td></tr>
    </table>

    @if ($quotation->notes)
        <h2>Notes</h2>
        <div class="terms">{!! nl2br(e($quotation->notes)) !!}</div>
    @endif

    @if ($quotation->terms)
        <h2>Terms &amp; conditions</h2>
        <div class="terms">{!! nl2br(e($quotation->terms)) !!}</div>
    @endif

    <p class="muted" style="margin-top: 26px">
        Prepared by {{ $quotation->preparer?->name ?? ($company?->name ?? 'Zytech Contractors') }}
    </p>
</body>
</html>
