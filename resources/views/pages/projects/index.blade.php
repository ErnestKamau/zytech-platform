@extends('layouts.website')

@section('title', 'Projects — Zytech Contractors')
@section('page-class', 'zy-page-projects')

@php
    $images = config('zyntech-media.images');
    $showreel = config('zyntech-media.videos.projects_showreel');
    $projects = [
        [
            'category' => 'Commercial',
            'title' => 'Commercial Courtyard — Stone & Paving',
            'meta' => 'Completed · Nairobi',
            'featured' => true,
            'image' => 'commercial_courtyard',
        ],
        [
            'category' => 'Site preparation',
            'title' => 'Site Preparation — Ballast Delivery',
            'meta' => 'In progress · Nairobi',
            'featured' => false,
            'image' => 'site_prep_ballast',
        ],
        [
            'category' => 'Paving',
            'title' => 'Hardscaping — Gravel Leveling',
            'meta' => 'In progress · Nairobi',
            'featured' => false,
            'image' => 'paving_gravel_leveling',
        ],
        [
            'category' => 'Structural',
            'title' => 'Covered Walkway — Steel Frame',
            'meta' => 'In progress · Nairobi',
            'featured' => false,
            'image' => 'structural_walkway',
        ],
        [
            'category' => 'Residential',
            'title' => 'Courtyard House — Exterior Finish',
            'meta' => 'Completed · Nairobi',
            'featured' => false,
            'image' => 'commercial_courtyard',
        ],
        [
            'category' => 'Landscaping',
            'title' => 'Garden Walkway — Pergola Structure',
            'meta' => 'Planning · Nairobi',
            'featured' => false,
            'image' => 'structural_walkway',
        ],
    ];
@endphp

@section('content')
    <div class="zy-container zy-projects-intro">
        <x-media.banner
            :video="asset($showreel['path'])"
            :poster="asset($showreel['poster'])"
            :alt="$showreel['alt']"
        >
            <p class="zy-eyebrow" style="color: rgb(255 255 255 / 0.75);">Portfolio</p>
            <h1 style="color: #fff;">Projects</h1>
            <p>Work photographed on Zytech sites across Nairobi and Kiambu — from groundworks to finished courtyards.</p>
        </x-media.banner>
    </div>

    <div class="zy-container zy-projects-grid">
        <div class="zy-grid zy-grid--3">
            @foreach ($projects as $project)
                <x-ui.card
                    interactive
                    :featured="$project['featured']"
                >
                    <x-media.cover
                        :src="asset($images[$project['image']]['path'])"
                        :alt="$images[$project['image']]['alt']"
                    />
                    <p class="zy-card__eyebrow">{{ $project['category'] }}</p>
                    <p class="zy-card__title">{{ $project['title'] }}</p>
                    <p class="zy-card__body">{{ $project['meta'] }}</p>
                </x-ui.card>
            @endforeach
        </div>
    </div>
@endsection
