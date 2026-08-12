@extends('emails.layouts.horizon', [
    'intent' => $intent ?? 'brand',
    'eyebrow' => $eyebrow ?? null,
    'heading' => $heading ?? $mailSubject,
    'preheader' => $preheader ?? \Illuminate\Support\Str::limit(strip_tags($mailBody), 120),
])

@section('content')
    @include('emails.partials.copy', [
        'text' => nl2br(e($mailBody)),
        'align' => 'left',
    ])

    @isset($ctaUrl)
        @include('emails.partials.cta', [
            'url' => $ctaUrl,
            'label' => $ctaLabel ?? 'Open Zytech',
        ])
    @endisset
@endsection
