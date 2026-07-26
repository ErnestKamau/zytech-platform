<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Account' }} — Zytech</title>
    @vite(['resources/css/portal/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script>
        document.documentElement.dataset.zyTheme = localStorage.getItem('zy-theme') || 'light';
    </script>
</head>
<body class="zy-portal-body">
    <div class="zy-portal">
        <aside class="zy-portal__nav">
            <a href="{{ route('home') }}" class="zy-portal__brand">Zytech</a>
            <nav class="zy-portal__links" aria-label="Account">
                <a href="{{ route('account.profile') }}" @class(['is-active' => request()->routeIs('account.profile')])>Profile</a>
                <a href="{{ route('account.security') }}" @class(['is-active' => request()->routeIs('account.security')])>Security</a>
                <a href="{{ route('account.sessions') }}" @class(['is-active' => request()->routeIs('account.sessions')])>Sessions</a>
                <a href="{{ route('account.settings') }}" @class(['is-active' => request()->routeIs('account.settings')])>Account</a>
            </nav>
        </aside>

        <main class="zy-portal__main">
            @if (session('status'))
                <div class="zy-alert zy-alert--success" role="status">{{ session('status') }}</div>
            @endif

            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>
</html>
