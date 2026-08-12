@extends('layouts.website')

@php
    $catalogue = app(\App\Domains\Service\Services\ServiceService::class)->findPublished($slug);
    $seo = $catalogue
        ? app(\App\Domains\Service\Services\ServiceSEOService::class)->forPage($catalogue)
        : ['title' => 'Service — Zytech Contractors', 'description' => ''];
@endphp

@section('title', $seo['title'])
@section('meta_description', $seo['description'])
@section('page-class', 'zy-page-services')

@section('content')
    <livewire:website.service-show :slug="$slug" />
@endsection
