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
        $asideKey = $asideImageKey ?? 'commercial_courtyard';
        $aside = $images[$asideKey] ?? $images['commercial_courtyard'];
        $asideHeadline = $asideHeadline ?? 'Sign in to your projects.';
        $asideSupport = $asideSupport ?? 'Track your build across Nairobi and beyond.';
        $showRegisterSteps = $showRegisterSteps ?? false;
    @endphp

    <x-ui.theme-toggle class="zy-auth__toggle" />

    <div class="zy-auth-shell">
        <aside class="zy-auth-aside">
            <div class="zy-auth-aside__media">
                <img src="{{ asset($aside['path']) }}" alt="{{ $aside['alt'] }}">
            </div>

            <div class="zy-auth-aside__top">
                <a href="{{ route('home') }}" class="zy-auth-aside__brand">
                    Zytech <span>Contractors</span>
                </a>
                <a href="{{ route('home') }}" class="zy-auth-aside__back">
                    <svg class="zy-icon zy-icon--sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    Back to website
                </a>
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
                        Verify email
                    </span>
                    <span class="zy-auth-aside__step">
                        <span class="zy-auth-aside__step-num">3</span>
                        Access portal
                    </span>
                </div>
            @endif
        </aside>

        <main class="zy-auth-main">
            <div class="zy-auth__mobile-brand">
                <a href="{{ route('home') }}" class="zy-auth__logo">Zytech</a>
                <p class="zy-auth__tagline">Contractors Platform</p>
            </div>

            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>
</html>
