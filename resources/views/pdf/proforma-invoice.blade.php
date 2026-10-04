@php
    $currency = $proforma->currency ?: 'KES';
    $money = fn ($value): string => number_format((float) $value, 2);
    $client = $proforma->client;
    $quotation = $proforma->quotation;
    $clientName = $client?->name ?? $quotation?->request?->full_name;
    $clientEmail = $client?->email ?? $quotation?->request?->email;
    $clientPhone = $client?->phone ?? $quotation?->request?->phone;
    $row = 0;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $proforma->reference_number }}</title>
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
        .rule { border-top: 2px solid #5c7349; margin: 16px 0; }
        .parties td { vertical-align: top; width: 50%; }
        .eyebrow { font-size: 9px; text-transform: uppercase; letter-spacing: .1em; color: #5c7349; margin: 0 0 4px; font-weight: bold; }
        .subject { font-size: 14px; font-weight: bold; margin: 18px 0 2px; }
        .items { margin-top: 10px; }
        .items th { background: #f4f1ec; font-size: 9px; text-transform: uppercase; letter-spacing: .06em; color: #4a443e; padding: 7px 6px; text-align: left; border-bottom: 1px solid #ddd5cc; }
        .items td { padding: 7px 6px; border-bottom: 1px solid #ece6df; vertical-align: top; }
        .items .num { text-align: right; white-space: nowrap; }
        .totals { width: 46%; margin-left: auto; margin-top: 12px; }
        .totals td { padding: 4px 6px; }
        .totals td.num { text-align: right; white-space: nowrap; }
        .totals .grand td { border-top: 2px solid #5c7349; font-weight: bold; font-size: 13px; color: #3b4b31; padding-top: 8px; }
        h2 { font-size: 12px; color: #3b4b31; margin: 22px 0 6px; text-transform: uppercase; letter-spacing: .06em; }
        .terms { font-size: 10px; color: #3d3833; }
        .payment-box { margin-top: 14px; padding: 10px 12px; background: #fafaf7; border: 1px solid #ece6df; }
        .payment-box td { padding: 1px 0; font-size: 10px; }
        .disclaimer { margin-top: 18px; padding: 8px 10px; border: 1px solid #ddd5cc; font-size: 9.5px; color: #6b6560; font-style: italic; }
        .footer { position: fixed; bottom: -28px; left: 0; right: 0; font-size: 9px; color: #8a837c; text-align: center; }
    </style>
</head>
<body>
    <div class="footer">
        {{ $company?->name ?? 'Zytech Contractors' }} · {{ $proforma->reference_number }}
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
                    @if ($company?->kra_pin) <br>KRA PIN: {{ $company->kra_pin }} @endif
                    @if ($company?->vat_number) · VAT No: {{ $company->vat_number }} @endif
                </div>
            </td>
            <td style="width: 45%">
                <p class="doc-title">PROFORMA INVOICE</p>
                <table class="doc-meta">
                    <tr><td class="label">Reference</td><td class="value">{{ $proforma->reference_number }}</td></tr>
                    <tr><td class="label">Date</td><td class="value">{{ ($proforma->issued_at ?? $proforma->created_at ?? now())->format('d M Y') }}</td></tr>
                    @if ($proforma->valid_until)
                        <tr><td class="label">Valid until</td><td class="value">{{ $proforma->valid_until->format('d M Y') }}</td></tr>
                    @endif
                    @if ($quotation)
                        <tr><td class="label">Quotation</td><td class="value">{{ $quotation->reference_number }}</td></tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <div class="rule"></div>

    <table class="parties">
        <tr>
            <td>
                <p class="eyebrow">Billed to</p>
                <strong>{{ $clientName ?? '—' }}</strong><br>
                @if ($clientEmail) {{ $clientEmail }}<br> @endif
                @if ($clientPhone) {{ $clientPhone }}<br> @endif
                @if ($client?->kra_pin) KRA PIN: {{ $client->kra_pin }} @endif
            </td>
            <td></td>
        </tr>
    </table>

    @if ($quotation)
        <p class="subject">{{ $quotation->title }}</p>
    @endif

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
            @forelse ($proforma->items as $item)
                <tr>
                    <td class="muted">{{ ++$row }}</td>
                    <td>
                        <strong>{{ $item->label }}</strong>
                        @if ($item->description && $item->description !== $item->label)
                            <br><span class="muted">{{ $item->description }}</span>
                        @endif
                    </td>
                    <td class="num">{{ rtrim(rtrim(number_format((float) $item->quantity, 2), '0'), '.') }}</td>
                    <td>{{ $item->unit }}</td>
                    <td class="num">{{ $money($item->unit_price) }}</td>
                    <td class="num">{{ $money($item->line_total) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="muted" style="text-align: center; padding: 18px">No line items.</td></tr>
            @endforelse
        </tbody>
    </table>

    <table class="totals">
        <tr><td>Subtotal</td><td class="num">{{ $currency }} {{ $money($proforma->subtotal) }}</td></tr>
        @if ((float) $proforma->discount_amount > 0)
            <tr><td>Discount</td><td class="num">− {{ $currency }} {{ $money($proforma->discount_amount) }}</td></tr>
        @endif
        <tr><td>VAT</td><td class="num">{{ $currency }} {{ $money($proforma->tax_amount) }}</td></tr>
        <tr class="grand"><td>Total</td><td class="num">{{ $currency }} {{ $money($proforma->total_amount) }}</td></tr>
    </table>

    @if ($company?->bank_name || $company?->mpesa_paybill)
        <table class="payment-box">
            <tr><td colspan="2"><strong>Payment details</strong></td></tr>
            @if ($company?->bank_name)
                <tr><td class="muted" style="width: 30%">Bank</td><td>{{ $company->bank_name }} — {{ $company->bank_branch }}</td></tr>
                <tr><td class="muted">Account name</td><td>{{ $company->bank_account_name }}</td></tr>
                <tr><td class="muted">Account number</td><td>{{ $company->bank_account_number }}</td></tr>
            @endif
            @if ($company?->mpesa_paybill)
                <tr><td class="muted" style="width: 30%">M-Pesa Paybill</td><td>{{ $company->mpesa_paybill }} — {{ $company->mpesa_account_name }}</td></tr>
            @endif
        </table>
    @endif

    @if ($proforma->payment_terms)
        <h2>Payment terms</h2>
        <div class="terms">{!! nl2br(e($proforma->payment_terms)) !!}</div>
    @endif

    @if ($proforma->notes)
        <h2>Notes</h2>
        <div class="terms">{!! nl2br(e($proforma->notes)) !!}</div>
    @endif

    <div class="disclaimer">
        This is a Proforma Invoice and is not a demand for payment nor a Tax Invoice for VAT purposes.
        A Tax Invoice will be issued separately in accordance with the applicable commercial terms.
    </div>
</body>
</html>
