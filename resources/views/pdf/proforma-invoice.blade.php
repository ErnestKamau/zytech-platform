<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $proforma->reference_number }}</title>
    <style>
        @page { margin: 32px 36px 48px; }
        body { font-family: DejaVu Sans, sans-serif; color: #1c1815; font-size: 11px; line-height: 1.45; }
        table { width: 100%; border-collapse: collapse; }
        .doc-muted { color: #6b6560; }
        .doc-header td { vertical-align: top; }
        .doc-brand { font-size: 20px; font-weight: bold; color: #3b4b31; margin: 0 0 4px; }
        .doc-title { font-size: 22px; font-weight: bold; letter-spacing: .08em; color: #3b4b31; text-align: right; margin: 0; }
        .doc-meta td { padding: 1px 0; font-size: 10.5px; }
        .doc-meta td.label { color: #6b6560; text-align: right; padding-right: 8px; }
        .doc-meta td.value { text-align: right; width: 1%; white-space: nowrap; }
        .doc-rule { border-top: 2px solid #5c7349; margin: 16px 0; }
        .doc-parties td { vertical-align: top; width: 50%; }
        .doc-eyebrow { font-size: 9px; text-transform: uppercase; letter-spacing: .1em; color: #5c7349; margin: 0 0 4px; font-weight: bold; }
        .doc-subject { font-size: 14px; font-weight: bold; margin: 18px 0 2px; }
        .doc-items { margin-top: 10px; }
        .doc-items th { background: #f4f1ec; font-size: 9px; text-transform: uppercase; letter-spacing: .06em; color: #4a443e; padding: 7px 6px; text-align: left; border-bottom: 1px solid #ddd5cc; }
        .doc-items td { padding: 7px 6px; border-bottom: 1px solid #ece6df; vertical-align: top; }
        .doc-items .num { text-align: right; white-space: nowrap; }
        .doc-totals { width: 46%; margin-left: auto; margin-top: 12px; }
        .doc-totals td { padding: 4px 6px; }
        .doc-totals td.num { text-align: right; white-space: nowrap; }
        .doc-totals .grand td { border-top: 2px solid #5c7349; font-weight: bold; font-size: 13px; color: #3b4b31; padding-top: 8px; }
        .doc-h2 { font-size: 12px; color: #3b4b31; margin: 22px 0 6px; text-transform: uppercase; letter-spacing: .06em; }
        .doc-terms { font-size: 10px; color: #3d3833; }
        .doc-payment-box { margin-top: 14px; padding: 10px 12px; background: #fafaf7; border: 1px solid #ece6df; }
        .doc-payment-box td { padding: 1px 0; font-size: 10px; }
        .doc-disclaimer { margin-top: 18px; padding: 8px 10px; border: 1px solid #ddd5cc; font-size: 9.5px; color: #6b6560; font-style: italic; }
        .doc-footer { position: fixed; bottom: -28px; left: 0; right: 0; font-size: 9px; color: #8a837c; text-align: center; }
    </style>
</head>
<body>
    @include('components.documents.proforma-sheet', ['proforma' => $proforma, 'company' => $company])
</body>
</html>
