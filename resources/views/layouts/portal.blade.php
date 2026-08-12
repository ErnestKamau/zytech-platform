<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
        <meta name="user-id" content="{{ auth()->id() }}">
    @endauth
    <title>{{ $title ?? 'Portal' }} — Zytech</title>
    @vite(['resources/css/portal/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script>
        document.documentElement.dataset.zyTheme = localStorage.getItem('zy-theme') || 'light';
    </script>
</head>
<body
    class="zy-portal-body"
    x-data="{ dark: localStorage.getItem('zy-theme') === 'dark' }"
    x-bind:data-zy-theme="dark ? 'dark' : 'light'"
    x-effect="
        localStorage.setItem('zy-theme', dark ? 'dark' : 'light');
        document.documentElement.dataset.zyTheme = dark ? 'dark' : 'light';
    "
>
    <div class="zy-portal">
        <aside class="zy-portal__nav">
            <a href="{{ route('home') }}" class="zy-portal__brand">
                Zytech <span>Contractors</span>
            </a>

            @auth
                @php
                    $hasPortal = auth()->user()->clientProfile?->portal_access_granted_at !== null;
                @endphp

                @if ($hasPortal || request()->routeIs('portal.*'))
                    <p class="zy-portal__section">Workspace</p>
                    <nav class="zy-portal__links" aria-label="Client portal">
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

            <p class="zy-portal__section">Account</p>
            <nav class="zy-portal__links" aria-label="Account">
                <a href="{{ route('account.profile') }}" @class(['is-active' => request()->routeIs('account.profile')])>Profile</a>
                <a href="{{ route('account.security') }}" @class(['is-active' => request()->routeIs('account.security')])>Security</a>
                <a href="{{ route('account.sessions') }}" @class(['is-active' => request()->routeIs('account.sessions')])>Sessions</a>
                <a href="{{ route('account.settings') }}" @class(['is-active' => request()->routeIs('account.settings')])>Settings</a>
            </nav>

            <div class="zy-portal__nav-foot">
                @auth
                    <div class="zy-portal__user">
                        <span class="zy-portal__user-name">{{ auth()->user()->name }}</span>
                        <span class="zy-portal__user-meta">{{ auth()->user()->email }}</span>
                    </div>
                @endauth
                <div class="zy-portal__toolbar">
                    <a href="{{ route('home') }}" class="zy-btn zy-btn--ghost zy-btn--sm">Public site</a>
                    <x-ui.theme-toggle />
                </div>
            </div>
        </aside>

        <main class="zy-portal__main">
            <div class="zy-portal__main-inner">
                @if (session('status'))
                    <div class="zy-alert zy-alert--success" role="status">{{ session('status') }}</div>
                @endif

                {{ $slot }}
            </div>
        </main>
    </div>

    <div
        id="zy-toast-host"
        class="zy-toast-host"
        x-data="zyToasts()"
        x-on:zy-toast.window="push($event.detail)"
        aria-live="polite"
        aria-atomic="true"
    >
        <template x-for="toast in items" :key="toast.id">
            <div class="zy-toast zy-toast--info" role="status">
                <p class="zy-toast__title" x-text="toast.title"></p>
                <p class="zy-toast__body" x-text="toast.body"></p>
            </div>
        </template>
    </div>

    @livewireScripts
</body>
</html>
