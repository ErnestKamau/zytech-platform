@props(['platform' => [], 'company' => null])

@php
    $branding = $platform['branding'] ?? null;
    $headerNav = $platform['headerNav'] ?? null;
    $short = $branding?->shortName ?? 'Zytech';
    $rest = trim(str_replace($short, '', $branding?->companyName ?? 'Zytech Contractors'));

    if ($headerNav && count($headerNav->items) > 0) {
        $navItems = [];
        foreach ($headerNav->items as $item) {
            $navItems[] = [
                'href' => $item['href'],
                'label' => $item['label'],
                'target' => $item['target'] ?? '_self',
                'current' => url()->current() === $item['href'],
            ];
        }
    } else {
        $navItems = [
            ['href' => route('projects.index'), 'label' => 'Projects', 'target' => '_self', 'current' => request()->routeIs('projects.*')],
            ['href' => route('products.index'), 'label' => 'Products', 'target' => '_self', 'current' => request()->routeIs('products.*')],
            ['href' => route('services.index'), 'label' => 'Services', 'target' => '_self', 'current' => request()->routeIs('services.*')],
            ['href' => route('knowledge.index'), 'label' => 'Knowledge', 'target' => '_self', 'current' => request()->routeIs('knowledge.*')],
            ['href' => route('about'), 'label' => 'About', 'target' => '_self', 'current' => request()->routeIs('about')],
            ['href' => route('contact'), 'label' => 'Contact', 'target' => '_self', 'current' => request()->routeIs('contact')],
        ];
    }

    $portalHome = null;
    $accountLabel = 'Account';
    if (auth()->check()) {
        $portalHome = auth()->user()->clientProfile?->portal_access_granted_at
            ? route('portal.dashboard')
            : route('account.profile');
        $accountLabel = auth()->user()->clientProfile?->portal_access_granted_at ? 'Portal' : 'Account';
    }
@endphp

<header
    class="zy-header"
    :class="{ 'is-condensed': condensed }"
    x-data="{
        menuOpen: false,
        condensed: false,
        syncCondensed() {
            this.condensed = window.scrollY > 32;
        },
    }"
    x-init="syncCondensed()"
    @scroll.window.passive="syncCondensed()"
    x-effect="document.documentElement.classList.toggle('zy-nav-open', menuOpen)"
    @keydown.escape.window="menuOpen = false"
>
    <div class="zy-container zy-header__inner">
        <a href="{{ route('home') }}" class="zy-nav__brand">
            {{ $short }} @if ($rest !== '')<span>{{ $rest }}</span>@endif
        </a>

        <nav class="zy-nav zy-header__nav" aria-label="Primary">
            @foreach ($navItems as $item)
                <a
                    href="{{ $item['href'] }}"
                    class="zy-nav__link"
                    @if ($item['current']) aria-current="page" @endif
                    @if ($item['target'] === '_blank') target="_blank" rel="noopener" @endif
                >{{ $item['label'] }}</a>
            @endforeach
        </nav>

        <div class="zy-header__actions">
            <x-ui.theme-toggle />
            <livewire:website.cart-badge />
            @auth
                @if (auth()->user()->canAccessPanel(filament()->getPanel('admin')))
                    <a href="{{ url('/admin') }}" class="zy-icon-btn zy-header__admin" aria-label="Admin">
                        <svg class="zy-icon zy-icon--sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                    </a>
                @endif
                <a href="{{ $portalHome }}" class="zy-icon-btn zy-header__account" aria-label="{{ $accountLabel }}">
                    <svg class="zy-icon zy-icon--sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                </a>
            @else
                <a href="{{ route('login') }}" class="zy-header__login">Sign in</a>
            @endauth
            <a href="{{ route('quote.index') }}" class="zy-btn zy-btn--primary zy-btn--sm zy-header__cta">Request a Quote</a>
            <button
                type="button"
                class="zy-icon-btn zy-header__menu"
                @click="menuOpen = !menuOpen"
                :aria-expanded="menuOpen.toString()"
                aria-controls="zy-mobile-nav"
                :aria-label="menuOpen ? 'Close menu' : 'Open menu'"
            >
                <span class="zy-header__menu-icon" :class="menuOpen && 'is-open'" aria-hidden="true"></span>
            </button>
        </div>
    </div>

    <div
        id="zy-mobile-nav"
        class="zy-header__panel"
        :aria-hidden="(!menuOpen).toString()"
        @click.self="menuOpen = false"
    >
        <div class="zy-header__sheet">
            <nav class="zy-header__sheet-nav" aria-label="Mobile">
                @foreach ($navItems as $item)
                    <a
                        href="{{ $item['href'] }}"
                        class="zy-header__sheet-link"
                        @if ($item['current']) aria-current="page" @endif
                        @if ($item['target'] === '_blank') target="_blank" rel="noopener" @endif
                        @click="menuOpen = false"
                    >{{ $item['label'] }}</a>
                @endforeach
            </nav>
            <div class="zy-header__sheet-actions">
                @auth
                    <a href="{{ $portalHome }}" class="zy-btn zy-btn--secondary" @click="menuOpen = false">
                        {{ $accountLabel }}
                    </a>
                @else
                    <a href="{{ route('login') }}" class="zy-btn zy-btn--secondary" @click="menuOpen = false">Sign in</a>
                @endauth
                <a href="{{ route('quote.index') }}" class="zy-btn zy-btn--primary" @click="menuOpen = false">Request a Quote</a>
            </div>
        </div>
    </div>
</header>
