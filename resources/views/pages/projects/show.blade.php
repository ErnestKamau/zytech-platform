@extends('layouts.website')

@php
    $catalogue = app(\App\Domains\Project\Services\ProjectService::class)->findPublished($slug);
    $seo = $catalogue
        ? app(\App\Domains\Project\Services\ProjectSEOService::class)->forPage($catalogue)
        : ['title' => 'Project — Zytech Contractors', 'description' => ''];
@endphp

@section('title', $seo['title'])
@section('meta_description', $seo['description'])
@section('page-class', 'zy-page-projects')

@section('content')
    <livewire:website.project-show :slug="$slug" />
@endsection
