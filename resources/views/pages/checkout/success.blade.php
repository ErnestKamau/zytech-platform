@extends('layouts.website')

@section('title', 'Order placed — Zytech Contractors')
@section('meta_description', 'Your Zytech catalogue order was placed.')
@section('page-class', 'zy-page-products')

@section('content')
    <livewire:website.checkout-success :order-number="$orderNumber" />
@endsection
