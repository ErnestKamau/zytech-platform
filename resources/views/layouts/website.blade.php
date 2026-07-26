<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Zytech Contractors')</title>
    @vite(['resources/css/website/app.css', 'resources/js/app.js'])
    <script>
        /* Apply saved theme before paint to avoid a light→dark flash. */
        document.documentElement.dataset.zyTheme = localStorage.getItem('zy-theme') || 'light';
    </script>
</head>
<body
    x-data="{ dark: localStorage.getItem('zy-theme') === 'dark' }"
    x-bind:data-zy-theme="dark ? 'dark' : 'light'"
    x-effect="
        localStorage.setItem('zy-theme', dark ? 'dark' : 'light');
        document.documentElement.dataset.zyTheme = dark ? 'dark' : 'light';
    "
>
    <div class="@yield('page-class', 'zy-page')">
        <x-layout.header />

        <main>
            @yield('content')
        </main>

        <x-layout.footer />
    </div>
</body>
</html>
