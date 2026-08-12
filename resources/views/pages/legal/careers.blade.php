@extends('layouts.website')

@section('title', 'Careers — Zytech Contractors')
@section('page-class', 'zy-page zy-page-legal')

@section('content')
    <section class="zy-section">
        <div class="zy-container zy-legal">
            <p class="zy-section__eyebrow">Careers</p>
            <h1>Build with Zytech</h1>
            <p>We occasionally hire site, design, and operations talent across Nairobi and Kiambu.</p>

            <div class="zy-alert zy-alert--success" role="status">
                Open roles are not listed on the site yet. This careers board is planned for a future release.
            </div>

            <h2>In the meantime</h2>
            <p>Send a short introduction and CV to our team. Tell us the discipline you work in and the counties you can cover.</p>
            <div class="zy-legal__actions">
                <a href="{{ route('contact') }}" class="zy-btn zy-btn--primary">Contact us</a>
                <a href="{{ route('about') }}" class="zy-btn zy-btn--secondary">About Zytech</a>
            </div>
        </div>
    </section>
@endsection
