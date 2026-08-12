@extends('layouts.website')

@section('title', 'Page not found — Zytech Contractors')
@section('page-class', 'zy-page zy-page-legal')

@section('content')
    <section class="zy-section">
        <div class="zy-container">
            <x-ui.empty-state
                class="zy-empty--hero"
                title="We couldn’t find that page."
                description="The link may be outdated, or the page may have moved. Try one of these instead."
                :lottie="asset('media/lottie/404-cat.lottie')"
            >
                <x-slot:actions>
                    <a href="{{ route('home') }}" class="zy-btn zy-btn--primary">Home</a>
                    <a href="{{ route('search') }}" class="zy-btn zy-btn--secondary">Search</a>
                    <a href="{{ route('contact') }}" class="zy-btn zy-btn--ghost">Contact</a>
                </x-slot:actions>
            </x-ui.empty-state>
        </div>
    </section>
@endsection
