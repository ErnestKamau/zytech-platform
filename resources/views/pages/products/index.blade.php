@extends('layouts.website')

@section('title', 'Products — Zytech Contractors')
@section('page-class', 'zy-page-products')

@section('content')
    <livewire:website.products-page :category="$category ?? null" />
@endsection
