@extends('layouts.website')

@section('title', 'Services — Zytech Contractors')
@section('page-class', 'zy-page-services')

@php
    $services = config('zyntech-services');
    $images = config('zyntech-media.images');
    $banner = $images['paving_gravel_leveling'];
    $process = config('zyntech-media.videos.services_process');
@endphp

@section('content')
    <div class="zy-container zy-services-intro">
        <x-media.banner :src="asset($banner['path'])" :alt="$banner['alt']">
            <p class="zy-eyebrow" style="color: rgb(255 255 255 / 0.75);">What we do</p>
            <h1 style="color: #fff;">Services</h1>
            <p>Design, estimate, approve, and build — one team on Kenyan soil from first sketch to handover.</p>
        </x-media.banner>
    </div>

    <section class="zy-section">
        <div class="zy-container">
            <div class="zy-grid zy-grid--3">
                @foreach ($services as $service)
                    <x-ui.card interactive id="{{ $service['slug'] }}">
                        @if ($service['image'] && isset($images[$service['image']]))
                            <x-media.cover
                                :src="asset($images[$service['image']]['path'])"
                                :alt="$images[$service['image']]['alt']"
                            />
                        @else
                            <span class="zy-icon-tile {{ $service['featured'] ? 'zy-icon-tile--gradient' : '' }}" aria-hidden="true">
                                <svg class="zy-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $service['icon'] }}" />
                                </svg>
                            </span>
                        @endif
                        <p class="zy-card__title" style="margin-top: var(--zy-space-2);">{{ $service['title'] }}</p>
                        <p class="zy-card__body">{{ $service['body'] }}</p>
                    </x-ui.card>
                @endforeach
            </div>
        </div>
    </section>

    <section class="zy-section zy-section--alt">
        <div class="zy-container">
            <div class="zy-section__header">
                <p class="zy-section__eyebrow">On site</p>
                <h2>How the work actually looks</h2>
                <p>Crew, materials, and structure — filmed on a live Zytech project.</p>
            </div>
            <x-media.banner
                class="zy-services-process"
                :video="asset($process['path'])"
                :poster="asset($process['poster'])"
                :alt="$process['alt']"
            >
                <p class="zy-eyebrow" style="color: rgb(255 255 255 / 0.75);">Process</p>
                <h2 style="color: #fff;">From groundworks to structure</h2>
            </x-media.banner>
        </div>
    </section>

    <x-sections.cta>
        <a href="{{ route('contact') }}" class="zy-btn zy-btn--primary zy-btn--lg">Request a Quote</a>
        <a href="{{ route('projects.index') }}" class="zy-btn zy-btn--frost zy-btn--lg">Browse Projects</a>
    </x-sections.cta>
@endsection
