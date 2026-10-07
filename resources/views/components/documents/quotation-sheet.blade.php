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
<div class="doc-footer">
    {{ $company?->name ?? 'Zytech Contractors' }} · {{ $quotation->reference_number }}
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
                <div style="text-align: right"><span class="doc-draft">DRAFT — NOT YET ISSUED</span></div>
            @endif
        </td>
    </tr>
</table>

<div class="doc-rule"></div>

<table class="doc-parties">
    <tr>
        <td>
            <p class="doc-eyebrow">Prepared for</p>
            <strong>{{ $clientName ?? '—' }}</strong><br>
            @if ($clientEmail) {{ $clientEmail }}<br> @endif
            @if ($clientPhone) {{ $clientPhone }}<br> @endif
        </td>
        <td>
            @if ($siteLocation !== '' || $request?->project_type)
                <p class="doc-eyebrow">Project</p>
                @if ($request?->project_type) {{ $request->project_type->label() }}<br> @endif
                @if ($siteLocation !== '') {{ $siteLocation }} @endif
            @endif
        </td>
    </tr>
</table>

<p class="doc-subject">{{ $quotation->title }}</p>

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
        @forelse ($groups as $group)
            @if ($group['title'])
                <tr class="group"><td colspan="6">{{ $group['title'] }}</td></tr>
            @endif
            @foreach ($group['items'] as $item)
                <tr>
                    <td class="doc-muted">{{ ++$row }}</td>
                    <td>
                        <strong>{{ $item->label }}</strong>
                        @if ($item->is_optional) <span class="doc-optional">(optional — not included in total)</span> @endif
                        @if ($item->description && $item->description !== $item->label)
                            <br><span class="doc-muted">{{ $item->description }}</span>
                        @endif
                    </td>
                    <td class="num">{{ rtrim(rtrim(number_format((float) $item->quantity, 2), '0'), '.') }}</td>
                    <td>{{ $item->unit }}</td>
                    <td class="num">{{ $money($item->unit_price) }}</td>
                    <td class="num">{{ $money($item->line_total) }}</td>
                </tr>
            @endforeach
        @empty
            <tr><td colspan="6" class="doc-muted" style="text-align: center; padding: 18px">No line items yet.</td></tr>
        @endforelse
    </tbody>
</table>

<table class="doc-totals">
    <tr><td>Subtotal</td><td class="num">{{ $currency }} {{ $money($quotation->subtotal) }}</td></tr>
    @if ((float) $quotation->discount_amount > 0)
        <tr><td>Discount</td><td class="num">− {{ $currency }} {{ $money($quotation->discount_amount) }}</td></tr>
    @endif
    <tr><td>VAT ({{ rtrim(rtrim(number_format((float) $quotation->tax_rate, 2), '0'), '.') }}%)</td><td class="num">{{ $currency }} {{ $money($quotation->tax_amount) }}</td></tr>
    <tr class="grand"><td>Total</td><td class="num">{{ $currency }} {{ $money($quotation->total_amount) }}</td></tr>
</table>

@if ($quotation->notes)
    <h2 class="doc-h2">Notes</h2>
    <div class="doc-terms">{!! nl2br(e($quotation->notes)) !!}</div>
@endif

@if ($quotation->terms)
    <h2 class="doc-h2">Terms &amp; conditions</h2>
    <div class="doc-terms">{!! nl2br(e($quotation->terms)) !!}</div>
@endif

<p class="doc-muted" style="margin-top: 26px">
    Prepared by {{ $quotation->preparer?->name ?? ($company?->name ?? 'Zytech Contractors') }}
</p>
