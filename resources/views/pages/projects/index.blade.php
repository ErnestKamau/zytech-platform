@extends('layouts.website')

@section('title', 'Projects — Zytech Contractors')
@section('page-class', 'zy-page-projects')

@php
    $projects = [
        [
            'category' => 'Residential',
            'title' => 'Kilimani Modern Villa',
            'meta' => 'Completed 2026 · Nairobi',
            'featured' => false,
        ],
        [
            'category' => 'Commercial',
            'title' => 'Two Rivers Office Park',
            'meta' => 'In progress · Nairobi',
            'featured' => true,
        ],
        [
            'category' => 'Renovation',
            'title' => 'Karen Courtyard House',
            'meta' => 'Planning · Nairobi',
            'featured' => false,
        ],
        [
            'category' => 'Interior',
            'title' => 'Westlands Penthouse Fit-out',
            'meta' => 'Completed 2025 · Nairobi',
            'featured' => false,
        ],
        [
            'category' => 'Structural',
            'title' => 'Ruiru Light Industrial Bay',
            'meta' => 'In progress · Kiambu',
            'featured' => false,
        ],
        [
            'category' => 'Residential',
            'title' => 'Lavington Garden Extension',
            'meta' => 'Completed 2025 · Nairobi',
            'featured' => false,
        ],
    ];
@endphp

@section('content')
    <div class="zy-container zy-projects-intro">
        <p class="zy-eyebrow">Portfolio</p>
        <h1>Projects</h1>
        <p>Placeholder project cards composed from design-system tokens and components. Real media and CMS content come later.</p>
    </div>

    <div class="zy-container zy-projects-grid">
        <div class="zy-grid zy-grid--3">
            @foreach ($projects as $project)
                <x-ui.card
                    interactive
                    :featured="$project['featured']"
                >
                    <div class="zy-project-card__cover" aria-hidden="true"></div>
                    <p class="zy-card__eyebrow">{{ $project['category'] }}</p>
                    <p class="zy-card__title">{{ $project['title'] }}</p>
                    <p class="zy-card__body">{{ $project['meta'] }}</p>
                </x-ui.card>
            @endforeach
        </div>
    </div>
@endsection
