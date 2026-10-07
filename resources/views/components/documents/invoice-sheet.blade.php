@php
    use App\Core\Enums\InvoiceStatus;

    $currency = $invoice->currency ?: 'KES';
    $money = fn ($value): string => number_format((float) $value, 2);
    $client = $invoice->client;
    $quotation = $invoice->quotation;
    $clientName = $client?->name ?? $quotation?->request?->full_name;
    $clientEmail = $client?->email ?? $quotation?->request?->email;
    $clientPhone = $client?->phone ?? $quotation?->request?->phone;
    $row = 0;
@endphp
<div class="doc-footer">
    {{ $company?->name ?? 'Zytech Contractors' }} · {{ $invoice->reference_number }}
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
            <p class="doc-title">TAX INVOICE</p>
            <table class="doc-meta">
                <tr><td class="label">Invoice No.</td><td class="value">{{ $invoice->reference_number }}</td></tr>
                <tr><td class="label">Date</td><td class="value">{{ ($invoice->issued_at ?? $invoice->created_at ?? now())->format('d M Y') }}</td></tr>
                @if ($invoice->due_date)
                    <tr><td class="label">Due date</td><td class="value">{{ $invoice->due_date->format('d M Y') }}</td></tr>
                @endif
                @if ($quotation)
                    <tr><td class="label">Quotation</td><td class="value">{{ $quotation->reference_number }}</td></tr>
                @endif
                @if ($invoice->salesOrder)
                    <tr><td class="label">Order</td><td class="value">{{ $invoice->salesOrder->reference_number }}</td></tr>
                @endif
            </table>
            @if ($invoice->status === InvoiceStatus::Draft)
                <div style="text-align: right"><span class="doc-draft">DRAFT — NOT YET ISSUED</span></div>
            @endif
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
        <td>
            @if ($invoice->payment_terms)
                <p class="doc-eyebrow">Payment terms</p>
                {{ $invoice->payment_terms }}
            @endif
        </td>
    </tr>
</table>

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
        @forelse ($invoice->items as $item)
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
    <tr><td>Subtotal</td><td class="num">{{ $currency }} {{ $money($invoice->subtotal) }}</td></tr>
    @if ((float) $invoice->discount_amount > 0)
        <tr><td>Discount</td><td class="num">− {{ $currency }} {{ $money($invoice->discount_amount) }}</td></tr>
    @endif
    <tr><td>VAT</td><td class="num">{{ $currency }} {{ $money($invoice->tax_amount) }}</td></tr>
    <tr class="grand"><td>Total</td><td class="num">{{ $currency }} {{ $money($invoice->total_amount) }}</td></tr>
    <tr><td>Amount paid</td><td class="num">{{ $currency }} {{ $money($invoice->amount_paid) }}</td></tr>
    <tr class="grand"><td>Amount due</td><td class="num">{{ $currency }} {{ $money($invoice->amount_due) }}</td></tr>
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

@if ($invoice->payments->isNotEmpty())
    <h2 class="doc-h2">Payment history</h2>
    <table class="doc-items">
        <thead>
            <tr>
                <th>Date</th>
                <th>Method</th>
                <th>Reference</th>
                <th class="num">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->payments as $payment)
                <tr>
                    <td>{{ $payment->paid_at?->format('d M Y') ?? $payment->created_at?->format('d M Y') }}</td>
                    <td>{{ $payment->method ?? '—' }}</td>
                    <td>{{ $payment->reference ?? '—' }}</td>
                    <td class="num">{{ $currency }} {{ $money($payment->amount) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

@if ($invoice->notes)
    <h2 class="doc-h2">Notes</h2>
    <div class="doc-terms">{!! nl2br(e($invoice->notes)) !!}</div>
@endif
