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
            ['href' => route('home'), 'label' => 'Home', 'target' => '_self', 'current' => request()->routeIs('home')],
            ['href' => route('projects.index'), 'label' => 'Projects', 'target' => '_self', 'current' => request()->routeIs('projects.*')],
            ['href' => route('services.index'), 'label' => 'Services', 'target' => '_self', 'current' => request()->routeIs('services.*')],
            ['href' => route('knowledge.index'), 'label' => 'Knowledge', 'target' => '_self', 'current' => request()->routeIs('knowledge.*')],
            ['href' => route('about'), 'label' => 'About', 'target' => '_self', 'current' => request()->routeIs('about')],
            ['href' => route('contact'), 'label' => 'Contact', 'target' => '_self', 'current' => request()->routeIs('contact')],
        ];
    }
@endphp

<header
    class="zy-header"
    x-data="{ menuOpen: false }"
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
            @auth
                <a href="{{ route('account.profile') }}" class="zy-btn zy-btn--secondary zy-btn--sm zy-header__cta">Account</a>
            @else
                <a href="{{ route('login') }}" class="zy-btn zy-btn--secondary zy-btn--sm zy-header__cta">Sign in</a>
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
                    <a href="{{ route('account.profile') }}" class="zy-btn zy-btn--secondary" @click="menuOpen = false">Account</a>
                @else
                    <a href="{{ route('login') }}" class="zy-btn zy-btn--secondary" @click="menuOpen = false">Sign in</a>
                @endauth
                <a href="{{ route('quote.index') }}" class="zy-btn zy-btn--primary" @click="menuOpen = false">Request a Quote</a>
            </div>
        </div>
    </div>
</header>
