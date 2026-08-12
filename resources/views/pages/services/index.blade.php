@extends('layouts.website')

@section('title', 'Services — Zytech Contractors')
@section('page-class', 'zy-page-services')

@section('content')
    <livewire:website.services-page :category="$category ?? null" />
@endsection
