@extends('layouts.website')

@section('title', 'Quotation received — Zytech Contractors')
@section('page-class', 'zy-page-quote')

@section('content')
    <div class="zy-container zy-quote-success">
        <p class="zy-section__eyebrow">Thank you</p>
        <h1>Request received</h1>
        <p>Your reference number is <strong>{{ $reference }}</strong>. Our sales team will respond within one business day.</p>
        <div style="display: flex; flex-wrap: wrap; gap: var(--zy-space-3); margin-top: var(--zy-space-6);">
            <a href="{{ route('quote.track', $reference) }}" class="zy-btn zy-btn--primary">Track status</a>
            <a href="{{ route('home') }}" class="zy-btn zy-btn--secondary">Back to home</a>
        </div>
    </div>
@endsection
