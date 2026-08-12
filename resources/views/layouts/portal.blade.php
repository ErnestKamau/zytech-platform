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
        (function () {
            var root = document.documentElement;
            root.dataset.zyTheme = localStorage.getItem('zy-theme') || 'light';
            var collapsed = (localStorage.getItem('zy-portal-nav-collapsed') ?? '1') === '1';
            root.dataset.zyPortalNav = collapsed ? 'collapsed' : 'expanded';
        })();
    </script>
</head>
<body
    class="zy-portal-body"
    x-data="{
        dark: localStorage.getItem('zy-theme') === 'dark',
        navOpen: false,
        navCollapsed: (localStorage.getItem('zy-portal-nav-collapsed') ?? '1') === '1',
        isMobileNav() {
            return window.matchMedia('(max-width: 960px)').matches;
        },
        toggleNav() {
            if (this.isMobileNav()) {
                this.navOpen = !this.navOpen;
                return;
            }
            this.navCollapsed = !this.navCollapsed;
        },
        navIsExpanded() {
            return this.isMobileNav() ? this.navOpen : !this.navCollapsed;
        },
        menuIconOpen() {
            return this.isMobileNav() && this.navOpen;
        },
    }"
    x-bind:data-zy-theme="dark ? 'dark' : 'light'"
    x-effect="
        localStorage.setItem('zy-theme', dark ? 'dark' : 'light');
        document.documentElement.dataset.zyTheme = dark ? 'dark' : 'light';
        localStorage.setItem('zy-portal-nav-collapsed', navCollapsed ? '1' : '0');
        document.documentElement.dataset.zyPortalNav = navCollapsed ? 'collapsed' : 'expanded';
        document.documentElement.classList.toggle('zy-portal-nav-open', navOpen);
    "
    @keydown.escape.window="navOpen = false"
>
    @php
        $user = auth()->user();
        $hasPortal = $user?->clientProfile?->portal_access_granted_at !== null;
        $initials = collect(explode(' ', (string) ($user?->name ?? 'Z')))
            ->filter()
            ->take(2)
            ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');
        $helloName = explode(' ', (string) ($user?->name ?? 'there'))[0] ?: 'there';
        $pageTitle = $title ?? 'Portal';
        $avatarUrl = $user?->avatarUrl();
    @endphp

    <div class="zy-portal-shell">
        <div class="zy-portal-shell__glow zy-portal-shell__glow--a" aria-hidden="true"></div>
        <div class="zy-portal-shell__glow zy-portal-shell__glow--b" aria-hidden="true"></div>

        <div
            class="zy-portal"
            x-init="$nextTick(() => { $el.classList.add('zy-portal--ready') })"
            :class="{
                'is-nav-open': navOpen,
                'is-nav-collapsed': navCollapsed,
                'is-nav-expanded': !navCollapsed,
            }"
        >
            <div class="zy-portal__backdrop" @click="navOpen = false" aria-hidden="true"></div>

            <aside class="zy-portal__nav" id="zy-portal-nav">
                @auth
                    <a
                        href="{{ route('account.profile') }}"
                        class="zy-portal__user zy-portal__user--header"
                        title="{{ $user->name }}"
                        @click="navOpen = false"
                    >
                        @if ($avatarUrl)
                            <img src="{{ $avatarUrl }}" alt="" class="zy-portal__avatar zy-portal__avatar--image">
                        @else
                            <span class="zy-portal__avatar" aria-hidden="true">{{ $initials }}</span>
                        @endif
                        <div class="zy-portal__user-copy">
                            <span class="zy-portal__user-name">{{ $user->name }}</span>
                            <span class="zy-portal__user-meta">{{ $user->email }}</span>
                        </div>
                    </a>

                    @if ($hasPortal || request()->routeIs('portal.*'))
                        <p class="zy-portal__section">Workspace</p>
                        <nav class="zy-portal__links" aria-label="Client portal">
                            <x-portal.nav-link href="{{ route('portal.dashboard') }}" label="Dashboard" icon="home" :active="request()->routeIs('portal.dashboard')" @click="navOpen = false" />
                            <x-portal.nav-link href="{{ route('portal.projects') }}" label="Projects" icon="folder" :active="request()->routeIs('portal.projects')" @click="navOpen = false" />
                            <x-portal.nav-link href="{{ route('portal.quotations') }}" label="Quotations" icon="document" :active="request()->routeIs('portal.quotations')" @click="navOpen = false" />
                            <x-portal.nav-link href="{{ route('portal.documents') }}" label="Documents" icon="inbox" :active="request()->routeIs('portal.documents')" @click="navOpen = false" />
                            <x-portal.nav-link href="{{ route('portal.messages') }}" label="Messages" icon="chat" :active="request()->routeIs('portal.messages')" @click="navOpen = false" />
                            <x-portal.nav-link href="{{ route('portal.meetings') }}" label="Meetings" icon="calendar" :active="request()->routeIs('portal.meetings')" @click="navOpen = false" />
                            <x-portal.nav-link href="{{ route('portal.support') }}" label="Support" icon="ticket" :active="request()->routeIs('portal.support')" @click="navOpen = false" />
                            <x-portal.nav-link href="{{ route('portal.notifications') }}" label="Notifications" icon="bell" :active="request()->routeIs('portal.notifications')" @click="navOpen = false" />
                            <x-portal.nav-link href="{{ route('portal.timeline') }}" label="Timeline" icon="clock" :active="request()->routeIs('portal.timeline')" @click="navOpen = false" />
                        </nav>
                    @endif
                @endauth

                <p class="zy-portal__section">Account</p>
                <nav class="zy-portal__links" aria-label="Account">
                    <x-portal.nav-link href="{{ route('account.profile') }}" label="Profile" icon="user" :active="request()->routeIs('account.profile')" @click="navOpen = false" />
                    <x-portal.nav-link href="{{ route('account.security') }}" label="Security" icon="shield" :active="request()->routeIs('account.security')" @click="navOpen = false" />
                    <x-portal.nav-link href="{{ route('account.sessions') }}" label="Sessions" icon="sessions" :active="request()->routeIs('account.sessions')" @click="navOpen = false" />
                    <x-portal.nav-link href="{{ route('account.settings') }}" label="Settings" icon="cog" :active="request()->routeIs('account.settings')" @click="navOpen = false" />
                </nav>

                <div class="zy-portal__nav-foot">
                    <a href="{{ route('home') }}" class="zy-portal__foot-item" title="Public site" aria-label="Public site">
                        <x-portal.icon name="globe" />
                        <span class="zy-portal__foot-label">Public site</span>
                    </a>

                    <x-portal.theme-switch class="zy-portal__foot-theme" />

                    @auth
                        <form method="POST" action="{{ route('logout') }}" class="zy-portal__logout">
                            @csrf
                            <button type="submit" class="zy-portal__foot-item zy-portal__foot-item--danger" title="Sign out" aria-label="Sign out">
                                <x-portal.icon name="logout" />
                                <span class="zy-portal__foot-label">Sign out</span>
                            </button>
                        </form>
                    @endauth
                </div>
            </aside>

            <div class="zy-portal__stage">
                <header class="zy-portal__topbar">
                    <div class="zy-portal__topbar-start">
                        <button
                            type="button"
                            class="zy-icon-btn zy-portal__menu"
                            @click="toggleNav()"
                            :aria-expanded="navIsExpanded().toString()"
                            aria-controls="zy-portal-nav"
                            :aria-label="navIsExpanded() ? (isMobileNav() ? 'Close menu' : 'Collapse navigation') : (isMobileNav() ? 'Open menu' : 'Expand navigation')"
                            title="Toggle navigation"
                        >
                            <span class="zy-portal__menu-icon" aria-hidden="true" :class="{ 'is-open': menuIconOpen() }">
                                <span></span>
                                <span></span>
                            </span>
                        </button>
                        <div class="zy-portal__greeting">
                            <p class="zy-portal__hello">Hello, {{ $helloName }}</p>
                            <p class="zy-portal__hello-meta">{{ $pageTitle }}</p>
                        </div>
                    </div>
                    <div class="zy-portal__topbar-actions">
                        @if ($hasPortal || request()->routeIs('portal.*'))
                            <a
                                href="{{ route('portal.notifications') }}"
                                class="zy-icon-btn zy-portal__top-action"
                                aria-label="Notifications"
                            >
                                <x-portal.icon name="bell" />
                            </a>
                            <a href="{{ route('portal.meetings') }}" class="zy-btn zy-btn--primary zy-btn--sm zy-portal__create">
                                <x-portal.icon name="plus" />
                                Request meeting
                            </a>
                        @endif
                        @auth
                            @if ($avatarUrl)
                                <img src="{{ $avatarUrl }}" alt="" class="zy-portal__avatar zy-portal__avatar--top zy-portal__avatar--image">
                            @else
                                <span class="zy-portal__avatar zy-portal__avatar--top" aria-hidden="true">{{ $initials }}</span>
                            @endif
                        @endauth
                    </div>
                </header>

                <main class="zy-portal__main">
                    <div class="zy-portal__main-inner">
                        @if (session('status'))
                            <div class="zy-alert zy-alert--success" role="status">{{ session('status') }}</div>
                        @endif

                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
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
