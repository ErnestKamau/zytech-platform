<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Zytech Contractors')</title>
    @vite(['resources/css/website/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="@yield('page-class', 'zy-page')">
        <x-layout.header />

        <main>
            @yield('content')
        </main>

        <x-layout.footer />
    </div>
</body>
</html>
