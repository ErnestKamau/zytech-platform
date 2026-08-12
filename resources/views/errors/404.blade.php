@extends('layouts.website')

@section('title', 'Page not found — Zytech Contractors')
@section('page-class', 'zy-page zy-page-legal')

@section('content')
    <section class="zy-section">
        <div class="zy-container zy-legal">
            <p class="zy-section__eyebrow">404</p>
            <h1>We couldn’t find that page.</h1>
            <p class="zy-muted">The link may be outdated, or the page may have moved. Try one of these instead.</p>
            <div class="zy-legal__actions">
                <a href="{{ route('home') }}" class="zy-btn zy-btn--primary">Home</a>
                <a href="{{ route('search') }}" class="zy-btn zy-btn--secondary">Search</a>
                <a href="{{ route('contact') }}" class="zy-btn zy-btn--ghost">Contact</a>
            </div>
        </div>
    </section>
@endsection
