@extends('layouts.website')

@section('title', 'Track quotation — Zytech Contractors')
@section('page-class', 'zy-page-quote')

@section('content')
    <livewire:website.track-quotation :reference="$reference" />
@endsection
