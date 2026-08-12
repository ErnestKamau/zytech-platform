<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Portal' }} — Zytech</title>
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

            @auth
                @php
                    $hasPortal = auth()->user()->clientProfile?->portal_access_granted_at !== null;
                @endphp

                @if ($hasPortal || request()->routeIs('portal.*'))
                    <nav class="zy-portal__links" aria-label="Client portal">
                        <p class="zy-portal__section">Workspace</p>
                        <a href="{{ route('portal.dashboard') }}" @class(['is-active' => request()->routeIs('portal.dashboard')])>Dashboard</a>
                        <a href="{{ route('portal.projects') }}" @class(['is-active' => request()->routeIs('portal.projects')])>Projects</a>
                        <a href="{{ route('portal.quotations') }}" @class(['is-active' => request()->routeIs('portal.quotations')])>Quotations</a>
                        <a href="{{ route('portal.documents') }}" @class(['is-active' => request()->routeIs('portal.documents')])>Documents</a>
                        <a href="{{ route('portal.messages') }}" @class(['is-active' => request()->routeIs('portal.messages')])>Messages</a>
                        <a href="{{ route('portal.meetings') }}" @class(['is-active' => request()->routeIs('portal.meetings')])>Meetings</a>
                        <a href="{{ route('portal.support') }}" @class(['is-active' => request()->routeIs('portal.support')])>Support</a>
                        <a href="{{ route('portal.notifications') }}" @class(['is-active' => request()->routeIs('portal.notifications')])>Notifications</a>
                        <a href="{{ route('portal.timeline') }}" @class(['is-active' => request()->routeIs('portal.timeline')])>Timeline</a>
                    </nav>
                @endif
            @endauth

            <nav class="zy-portal__links" aria-label="Account">
                <p class="zy-portal__section">Account</p>
                <a href="{{ route('account.profile') }}" @class(['is-active' => request()->routeIs('account.profile')])>Profile</a>
                <a href="{{ route('account.security') }}" @class(['is-active' => request()->routeIs('account.security')])>Security</a>
                <a href="{{ route('account.sessions') }}" @class(['is-active' => request()->routeIs('account.sessions')])>Sessions</a>
                <a href="{{ route('account.settings') }}" @class(['is-active' => request()->routeIs('account.settings')])>Settings</a>
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
