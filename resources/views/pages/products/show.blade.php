@extends('layouts.website')

@php
    $catalogue = app(\App\Domains\Product\Services\ProductService::class)->findPublished($slug);
    $title = $catalogue?->metaTitle
        ?: (($catalogue?->title ? $catalogue->title.' — Zytech Contractors' : null) ?? 'Product — Zytech Contractors');
    $description = $catalogue?->metaDescription ?: ($catalogue?->excerpt ?? '');
@endphp

@section('title', $title)
@section('meta_description', $description)
@section('page-class', 'zy-page-products')

@section('content')
    <livewire:website.product-show :slug="$slug" />
@endsection
