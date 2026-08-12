@extends('layouts.website')

@section('title', 'Careers — Zytech Contractors')
@section('page-class', 'zy-page zy-page-legal')

@section('content')
    <section class="zy-section">
        <div class="zy-container">
            <x-ui.empty-state
                class="zy-empty--hero"
                title="No open roles yet"
                description="We occasionally hire site, design, and operations talent across Nairobi and Kiambu. The careers board is planned for a future release — introduce yourself in the meantime."
                :lottie="asset('media/lottie/no-connection.lottie')"
            >
                <x-slot:actions>
                    <a href="{{ route('contact') }}" class="zy-btn zy-btn--primary">Contact us</a>
                    <a href="{{ route('about') }}" class="zy-btn zy-btn--secondary">About Zytech</a>
                </x-slot:actions>
            </x-ui.empty-state>
        </div>
    </section>
@endsection
