@extends('layouts.website')

@section('title', 'Terms & Conditions — Zytech Contractors')
@section('page-class', 'zy-page zy-page-legal')

@section('content')
    <article class="zy-section">
        <div class="zy-container zy-legal">
            <p class="zy-section__eyebrow">Legal</p>
            <h1>Terms &amp; Conditions</h1>
            <p class="zy-muted">Last updated {{ now()->toFormattedDateString() }}</p>

            <h2>Using this website</h2>
            <p>By using the Zytech website and client portal, you agree to these terms. If you do not agree, please do not use the services.</p>

            <h2>Quotations and estimates</h2>
            <p>Online quotation requests are invitations to treat. Formal quotations, validity periods, and acceptance are governed by the documents we issue and any signed agreement.</p>

            <h2>Accounts and portal access</h2>
            <p>Portal access is granted to authorised clients. You are responsible for keeping login credentials confidential and for activity under your account.</p>

            <h2>Content</h2>
            <p>Project photos, articles, and documents on this site remain Zytech’s intellectual property unless stated otherwise. You may not republish materials without permission.</p>

            <h2>Limitation of liability</h2>
            <p>Information on the public site is provided for general guidance. Construction advice on individual sites depends on surveys, approvals, and contracts. Nothing on this site replaces professional instruction for your project.</p>

            <h2>Changes</h2>
            <p>We may update these terms. Continued use after changes constitutes acceptance of the revised terms.</p>

            <h2>Contact</h2>
            <p>Questions about these terms: use the <a href="{{ route('contact') }}">contact page</a>.</p>
        </div>
    </article>
@endsection
