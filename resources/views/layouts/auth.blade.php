<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Zytech Contractors' }}</title>
    @vite(['resources/css/website/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script>
        document.documentElement.dataset.zyTheme = localStorage.getItem('zy-theme') || 'light';
    </script>
</head>
<body class="zy-auth-body">
    <div class="zy-auth">
        <header class="zy-auth__brand">
            <a href="{{ route('home') }}" class="zy-auth__logo">Zytech</a>
            <p class="zy-auth__tagline">Contractors Platform</p>
        </header>

        <main class="zy-auth__panel">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>
</html>
