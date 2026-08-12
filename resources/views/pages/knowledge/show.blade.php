@extends('layouts.website')

@php
    $catalogue = app(\App\Domains\Knowledge\Services\KnowledgeCentreService::class)->findPublished($slug);
    $seo = $catalogue
        ? app(\App\Domains\Knowledge\Services\ArticleSEOService::class)->forPage($catalogue)
        : ['title' => 'Article — Zytech Contractors', 'description' => ''];
@endphp

@section('title', $seo['title'])
@section('meta_description', $seo['description'])
@section('page-class', 'zy-page-knowledge')

@section('content')
    <livewire:website.article-show :slug="$slug" />
@endsection
