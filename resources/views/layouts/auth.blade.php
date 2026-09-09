<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Zytech Contractors' }}</title>
    @vite(['resources/css/website/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script>
        document.documentElement.dataset.zyTheme = localStorage.getItem('zy-theme') || 'light';
    </script>
</head>
<body
    class="zy-auth-body"
    x-data="{ dark: localStorage.getItem('zy-theme') === 'dark' }"
    x-bind:data-zy-theme="dark ? 'dark' : 'light'"
    x-effect="
        localStorage.setItem('zy-theme', dark ? 'dark' : 'light');
        document.documentElement.dataset.zyTheme = dark ? 'dark' : 'light';
    "
>
    @php
        $images = config('zyntech-media.images');
        $asideKey = $asideImageKey ?? 'hero_facade_dusk';
        $aside = $images[$asideKey] ?? $images['hero_facade_dusk'] ?? $images['commercial_courtyard'];
        $asideHeadline = $asideHeadline ?? 'Sign in to your projects.';
        $asideSupport = $asideSupport ?? 'Track your build across Nairobi and beyond.';
        $showRegisterSteps = $showRegisterSteps ?? false;
    @endphp

    <div class="zy-auth-stage">
        <div class="zy-auth-card">
            <aside class="zy-auth-aside">
                <div class="zy-auth-visual">
                    <div class="zy-auth-visual__media">
                        <img
                            src="{{ asset($aside['path']) }}"
                            alt="{{ $aside['alt'] }}"
                            loading="eager"
                            decoding="async"
                        >
                    </div>
                    <div class="zy-auth-visual__overlay" aria-hidden="true"></div>
                </div>

                <div class="zy-auth-aside__chrome">
                    <a href="{{ route('home') }}" class="zy-auth-aside__brand">
                        <span class="zy-auth-aside__mark" aria-hidden="true">Z</span>
                        <span class="zy-auth-aside__brand-text">Zytech <em>Contractors</em></span>
                    </a>

                    <div class="zy-auth-aside__chrome-actions">
                        <a href="{{ route('home') }}" class="zy-auth-aside__skip">Website</a>

                        <div class="zy-auth-theme zy-auth-theme--aside" role="group" aria-label="Color theme">
                            <button
                                type="button"
                                class="zy-auth-theme__option"
                                x-on:click="dark = false"
                                x-bind:class="{ 'is-active': !dark }"
                                :aria-pressed="!dark"
                                aria-label="Light mode"
                            >
                                <svg class="zy-icon zy-icon--sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                                </svg>
                            </button>
                            <button
                                type="button"
                                class="zy-auth-theme__option"
                                x-on:click="dark = true"
                                x-bind:class="{ 'is-active': dark }"
                                :aria-pressed="dark"
                                aria-label="Dark mode"
                            >
                                <svg class="zy-icon zy-icon--sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="zy-auth-aside__body">
                    <p class="zy-auth-aside__eyebrow">Contractor Access</p>
                    <h2 class="zy-auth-aside__headline">{{ $asideHeadline }}</h2>
                    <p class="zy-auth-aside__lead">{{ $asideSupport }}</p>
                </div>

                @if ($showRegisterSteps)
                    <div class="zy-auth-aside__steps" aria-hidden="true">
                        <span class="zy-auth-aside__step is-active">
                            <span class="zy-auth-aside__step-num">1</span>
                            Sign up
                        </span>
                        <span class="zy-auth-aside__step">
                            <span class="zy-auth-aside__step-num">2</span>
                            Verify
                        </span>
                        <span class="zy-auth-aside__step">
                            <span class="zy-auth-aside__step-num">3</span>
                            Access
                        </span>
                    </div>
                @endif
            </aside>

            <main class="zy-auth-main">
                <header class="zy-auth-toolbar">
                    <a href="{{ route('home') }}" class="zy-auth-toolbar__back">
                        <svg class="zy-icon zy-icon--sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        <span class="zy-auth-toolbar__back-label">Back</span>
                    </a>

                    <div class="zy-auth-theme" role="group" aria-label="Color theme">
                        <button
                            type="button"
                            class="zy-auth-theme__option"
                            x-on:click="dark = false"
                            x-bind:class="{ 'is-active': !dark }"
                            :aria-pressed="!dark"
                            aria-label="Light mode"
                        >
                            <svg class="zy-icon zy-icon--sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                            </svg>
                            <span>Light</span>
                        </button>
                        <button
                            type="button"
                            class="zy-auth-theme__option"
                            x-on:click="dark = true"
                            x-bind:class="{ 'is-active': dark }"
                            :aria-pressed="dark"
                            aria-label="Dark mode"
                        >
                            <svg class="zy-icon zy-icon--sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                            </svg>
                            <span>Dark</span>
                        </button>
                    </div>
                </header>

                <div class="zy-auth-main__body">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
