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
<div class="doc-footer">
    {{ $company?->name ?? 'Zytech Contractors' }} · {{ $proforma->reference_number }}
    @if ($company?->website) · {{ $company->website }} @endif
</div>

<table class="doc-header">
    <tr>
        <td style="width: 55%">
            <p class="doc-brand">{{ $company?->name ?? 'Zytech Contractors' }}</p>
            <div class="doc-muted">
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

<div class="doc-rule"></div>

<table class="doc-parties">
    <tr>
        <td>
            <p class="doc-eyebrow">Billed to</p>
            <strong>{{ $clientName ?? '—' }}</strong><br>
            @if ($clientEmail) {{ $clientEmail }}<br> @endif
            @if ($clientPhone) {{ $clientPhone }}<br> @endif
            @if ($client?->kra_pin) KRA PIN: {{ $client->kra_pin }} @endif
        </td>
        <td></td>
    </tr>
</table>

@if ($quotation)
    <p class="doc-subject">{{ $quotation->title }}</p>
@endif

<table class="doc-items">
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
                <td class="doc-muted">{{ ++$row }}</td>
                <td>
                    <strong>{{ $item->label }}</strong>
                    @if ($item->description && $item->description !== $item->label)
                        <br><span class="doc-muted">{{ $item->description }}</span>
                    @endif
                </td>
                <td class="num">{{ rtrim(rtrim(number_format((float) $item->quantity, 2), '0'), '.') }}</td>
                <td>{{ $item->unit }}</td>
                <td class="num">{{ $money($item->unit_price) }}</td>
                <td class="num">{{ $money($item->line_total) }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="doc-muted" style="text-align: center; padding: 18px">No line items.</td></tr>
        @endforelse
    </tbody>
</table>

<table class="doc-totals">
    <tr><td>Subtotal</td><td class="num">{{ $currency }} {{ $money($proforma->subtotal) }}</td></tr>
    @if ((float) $proforma->discount_amount > 0)
        <tr><td>Discount</td><td class="num">− {{ $currency }} {{ $money($proforma->discount_amount) }}</td></tr>
    @endif
    <tr><td>VAT</td><td class="num">{{ $currency }} {{ $money($proforma->tax_amount) }}</td></tr>
    <tr class="grand"><td>Total</td><td class="num">{{ $currency }} {{ $money($proforma->total_amount) }}</td></tr>
</table>

@if ($company?->bank_name || $company?->mpesa_paybill)
    <table class="doc-payment-box">
        <tr><td colspan="2"><strong>Payment details</strong></td></tr>
        @if ($company?->bank_name)
            <tr><td class="doc-muted" style="width: 30%">Bank</td><td>{{ $company->bank_name }} — {{ $company->bank_branch }}</td></tr>
            <tr><td class="doc-muted">Account name</td><td>{{ $company->bank_account_name }}</td></tr>
            <tr><td class="doc-muted">Account number</td><td>{{ $company->bank_account_number }}</td></tr>
        @endif
        @if ($company?->mpesa_paybill)
            <tr><td class="doc-muted" style="width: 30%">M-Pesa Paybill</td><td>{{ $company->mpesa_paybill }} — {{ $company->mpesa_account_name }}</td></tr>
        @endif
    </table>
@endif

@if ($proforma->payment_terms)
    <h2 class="doc-h2">Payment terms</h2>
    <div class="doc-terms">{!! nl2br(e($proforma->payment_terms)) !!}</div>
@endif

@if ($proforma->notes)
    <h2 class="doc-h2">Notes</h2>
    <div class="doc-terms">{!! nl2br(e($proforma->notes)) !!}</div>
@endif

<div class="doc-disclaimer">
    This is a Proforma Invoice and is not a demand for payment nor a Tax Invoice for VAT purposes.
    A Tax Invoice will be issued separately in accordance with the applicable commercial terms.
</div>
