@extends('layouts.website')

@section('title', ($companyProfile->name ?? 'Zytech Contractors').' — About')
@section('page-class', 'zy-page-about')

@section('content')
    <livewire:website.about-page />
@endsection
