@props(['platform' => [], 'company' => null])

@php
    $contact = \App\Domains\Company\Support\ShareCompany::contact($company, $platform['contact'] ?? config('zyntech-media.contact'));
    $branding = $platform['branding'] ?? null;
    $footerNav = $platform['footerNav'] ?? null;
    $services = array_slice(config('zyntech-services'), 0, 4);
    $short = $branding?->shortName ?? 'Zytech';
    $displayName = $company?->name ?? $branding?->companyName ?? 'Zytech Contractors';
    $rest = trim(str_replace($short, '', $displayName));
    $description = $company?->shortDescription ?: ($branding?->description ?? 'Precision-built spaces for residential and commercial clients across Nairobi, Kiambu, and nationwide — from first sketch to final handover.');
    $art = asset(config('zyntech-media.videos.hero_site_work.poster'));

    $socialIcons = [
        'facebook' => '<path fill="currentColor" d="M14 8.5h2.5V5.2h-2.6C11.7 5.2 11 6.8 11 8.7V10H9v3.3h2V20h3.4v-6.7h2.5l.5-3.3H14.4V9c0-.4.2-.5.6-.5H14z"/>',
        'instagram' => '<path fill="currentColor" d="M12 7.4A4.6 4.6 0 1 0 16.6 12 4.6 4.6 0 0 0 12 7.4zm0 7.6A3 3 0 1 1 15 12a3 3 0 0 1-3 3zm5.8-8.9a1.08 1.08 0 1 1-1.08-1.08A1.08 1.08 0 0 1 17.8 6.1zM12 4.8c1.3 0 1.46 0 2 .07a3.6 3.6 0 0 1 2.15.8 3.6 3.6 0 0 1 .8 2.15c.06.52.07.68.07 2s0 1.46-.07 2a3.6 3.6 0 0 1-.8 2.15 3.6 3.6 0 0 1-2.15.8c-.52.06-.68.07-2 .07s-1.46 0-2-.07a3.6 3.6 0 0 1-2.15-.8 3.6 3.6 0 0 1-.8-2.15C5.8 13.46 5.8 13.3 5.8 12s0-1.46.07-2a3.6 3.6 0 0 1 .8-2.15 3.6 3.6 0 0 1 2.15-.8C10.54 4.8 10.7 4.8 12 4.8zm0-1.8c-1.33 0-1.5 0-2.03.07a5.4 5.4 0 0 0-3.57 1.33 5.4 5.4 0 0 0-1.33 3.57C3 9.5 3 9.67 3 11v2c0 1.33 0 1.5.07 2.03a5.4 5.4 0 0 0 1.33 3.57 5.4 5.4 0 0 0 3.57 1.33c.53.06.7.07 2.03.07h2c1.33 0 1.5 0 2.03-.07a5.4 5.4 0 0 0 3.57-1.33 5.4 5.4 0 0 0 1.33-3.57C21 14.5 21 14.33 21 13v-2c0-1.33 0-1.5-.07-2.03a5.4 5.4 0 0 0-1.33-3.57 5.4 5.4 0 0 0-3.57-1.33C15.5 3 15.33 3 14 3h-2z"/>',
        'linkedin' => '<path fill="currentColor" d="M6.5 9.5H3.7V20h2.8V9.5zM5.1 4A1.65 1.65 0 1 0 5.1 7.3 1.65 1.65 0 0 0 5.1 4zM20.3 13.1c0-3.3-1.76-4.83-4.11-4.83A3.54 3.54 0 0 0 13 9.9V9.5H10.3V20H13v-5.6c0-1.48.28-2.91 2.11-2.91 1.8 0 1.83 1.68 1.83 3V20h2.8z"/>',
        'x' => '<path fill="currentColor" d="M17.6 4h2.7l-5.9 6.74L21.5 20h-5.2l-4.07-5.32L7.6 20H4.9l6.31-7.21L2.8 4h5.33l3.68 4.87L17.6 4zm-1 14.4h1.5L7.48 5.52H5.87L16.6 18.4z"/>',
        'youtube' => '<path fill="currentColor" d="M21.6 7.2a2.5 2.5 0 0 0-1.76-1.77C18.3 5.1 12 5.1 12 5.1s-6.3 0-7.84.33A2.5 2.5 0 0 0 2.4 7.2 26.2 26.2 0 0 0 2.1 12a26.2 26.2 0 0 0 .3 4.8 2.5 2.5 0 0 0 1.76 1.77C5.7 18.9 12 18.9 12 18.9s6.3 0 7.84-.33A2.5 2.5 0 0 0 21.6 16.8 26.2 26.2 0 0 0 21.9 12a26.2 26.2 0 0 0-.3-4.8zM10.1 15.4V8.6L15.6 12l-5.5 3.4z"/>',
    ];
    $socialLabels = [
        'facebook' => 'Facebook',
        'instagram' => 'Instagram',
        'linkedin' => 'LinkedIn',
        'x' => 'X',
        'youtube' => 'YouTube',
    ];
    $social = collect($platform['social'] ?? [])
        ->filter(fn ($url, $network) => filled($url) && isset($socialIcons[$network]));
@endphp

<footer class="zy-footer" style="--zy-footer-art: url('{{ $art }}')">
    <div class="zy-container">
        <div class="zy-footer__panel">
            <div class="zy-footer__grid">
                <div class="zy-footer__brand-col">
                    <p class="zy-footer__brand">{{ $short }} @if ($rest !== '')<span>{{ $rest }}</span>@endif</p>
                    <p class="zy-footer__copy">{{ $description }}</p>
                </div>

                <nav class="zy-footer__col zy-footer__col--navigate" aria-label="Navigate">
                    <p class="zy-footer__heading">Navigate</p>
                    @if ($footerNav && count($footerNav->items) > 0)
                        <div class="zy-footer__link-grid">
                            @foreach ($footerNav->items as $item)
                                <a href="{{ $item['href'] }}" @if ($item['target'] === '_blank') target="_blank" rel="noopener" @endif>{{ $item['label'] }}</a>
                            @endforeach
                        </div>
                    @else
                        <div class="zy-footer__link-grid">
                            <a href="{{ route('home') }}">Home</a>
                            <a href="{{ route('projects.index') }}">Projects</a>
                            <a href="{{ route('services.index') }}">Services</a>
                            <a href="{{ route('downloads.index') }}">Downloads</a>
                        </div>
                        <div class="zy-footer__link-grid">
                            <a href="{{ route('about') }}">About</a>
                            <a href="{{ route('careers') }}">Careers</a>
                            <a href="{{ route('contact') }}">Contact</a>
                        </div>
                    @endif
                </nav>

                <nav class="zy-footer__col" aria-label="Services">
                    <p class="zy-footer__heading">Services</p>
                    @foreach ($services as $service)
                        <a href="{{ route('services.index') }}#{{ $service['slug'] }}">{{ $service['title'] }}</a>
                    @endforeach
                </nav>

                <div class="zy-footer__col">
                    <p class="zy-footer__heading">Contact</p>
                    <p>{{ $contact['location'] }}</p>
                    <a href="mailto:{{ $contact['email'] }}">{{ $contact['email'] }}</a>
                    <a href="tel:{{ preg_replace('/\s+/', '', $contact['phone']) }}">{{ $contact['phone'] }}</a>
                    <a href="{{ route('quote.index') }}" class="zy-footer__cta">Request a Quote</a>
                </div>
            </div>

            <div class="zy-footer__bar">
                <p class="zy-footer__meta">&copy; {{ date('Y') }} {{ $displayName }}</p>
                <div class="zy-footer__bar-end">
                    <nav class="zy-footer__legal" aria-label="Legal">
                        <a href="{{ route('privacy') }}">Privacy</a>
                        <a href="{{ route('terms') }}">Terms</a>
                    </nav>
                    @if ($social->isNotEmpty())
                        <nav class="zy-footer__social" aria-label="Social">
                            @foreach ($social as $network => $url)
                                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $socialLabels[$network] }}">
                                    <svg viewBox="0 0 24 24" aria-hidden="true">{!! $socialIcons[$network] !!}</svg>
                                </a>
                            @endforeach
                        </nav>
                    @endif
                </div>
            </div>
        </div>
    </div>
</footer>
