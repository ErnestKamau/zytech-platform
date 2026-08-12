<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
        <meta name="user-id" content="{{ auth()->id() }}">
    @endauth
    <title>@yield('title', $platform['seo']->title ?? 'Zytech Contractors')</title>
    <meta name="description" content="@yield('meta_description', $platform['seo']->description ?? '')">
    @if (! empty($platform['seo']->keywords))
        <meta name="keywords" content="{{ $platform['seo']->keywords }}">
    @endif
    <link rel="canonical" href="@yield('canonical', url()->current())">
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('og_title', $platform['seo']->title ?? 'Zytech Contractors')">
    <meta property="og:description" content="@yield('og_description', $platform['seo']->description ?? '')">
    <meta property="og:url" content="@yield('canonical', url()->current())">
    @if (! empty($platform['seo']->ogImage))
        <meta property="og:image" content="{{ $platform['seo']->ogImage }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">
    @stack('structured_data')
    @vite(['resources/css/website/app.css', 'resources/js/app.js'])
    @livewireStyles
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
    <a class="zy-skip-link" href="#main-content">Skip to content</a>

    <div class="@yield('page-class', 'zy-page')">
        <x-layout.header :platform="$platform ?? []" :company="$companyProfile ?? null" />

        <main id="main-content">
            @yield('content')
        </main>

        <x-layout.footer :platform="$platform ?? []" :company="$companyProfile ?? null" />
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
