@props(['platform' => [], 'company' => null])

@php
    $contact = \App\Domains\Company\Support\ShareCompany::contact($company, $platform['contact'] ?? config('zyntech-media.contact'));
    $branding = $platform['branding'] ?? null;
    $footerNav = $platform['footerNav'] ?? null;
    $services = array_slice(config('zyntech-services'), 0, 4);
    $short = $branding?->shortName ?? 'Zytech';
    $displayName = $company?->name ?? $branding?->companyName ?? 'Zytech Contractors';
    $rest = trim(str_replace($short, '', $displayName));
    $tagline = $company?->tagline ?: ($branding?->tagline ?? 'Built on Kenyan soil');
    $description = $company?->shortDescription ?: ($branding?->description ?? 'Precision-built spaces for residential and commercial clients across Nairobi, Kiambu, and nationwide — from first sketch to final handover.');
@endphp

<footer class="zy-footer">
    <div class="zy-container zy-footer__grid">
        <div class="zy-footer__brand-col">
            <p class="zy-footer__brand">{{ $short }} @if ($rest !== '')<span>{{ $rest }}</span>@endif</p>
            <p class="zy-footer__tagline">{{ $tagline }}</p>
            <p class="zy-footer__copy">{{ $description }}</p>
        </div>

        <nav class="zy-footer__col" aria-label="Navigate">
            <p class="zy-footer__heading">Navigate</p>
            @if ($footerNav && count($footerNav->items) > 0)
                @foreach ($footerNav->items as $item)
                    <a href="{{ $item['href'] }}" @if ($item['target'] === '_blank') target="_blank" rel="noopener" @endif>{{ $item['label'] }}</a>
                @endforeach
            @else
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('projects.index') }}">Projects</a>
                <a href="{{ route('services.index') }}">Services</a>
                <a href="{{ route('about') }}">About</a>
                <a href="{{ route('contact') }}">Contact</a>
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
            <a href="{{ route('contact') }}" class="zy-footer__cta">Request a Quote</a>
        </div>
    </div>

    <div class="zy-footer__bar">
        <div class="zy-container">
            <p class="zy-footer__meta">&copy; {{ date('Y') }} {{ $displayName }} · {{ $contact['location'] }}</p>
        </div>
    </div>
</footer>
