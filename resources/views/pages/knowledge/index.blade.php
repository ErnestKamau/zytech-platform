@extends('layouts.website')

@section('title', 'Knowledge Centre — Zytech Contractors')
@section('page-class', 'zy-page-knowledge')

@section('content')
    <livewire:website.knowledge-page :category="$category ?? null" />
@endsection
