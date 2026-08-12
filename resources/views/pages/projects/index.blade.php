@extends('layouts.website')

@section('title', 'Projects — Zytech Contractors')
@section('page-class', 'zy-page-projects')

@section('content')
    <livewire:website.projects-page :category="$category ?? null" />
@endsection
