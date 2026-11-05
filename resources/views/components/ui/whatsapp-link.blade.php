@props([
    'number' => null,
    'message' => 'Hi Zytech, ',
])

@php
    $resolvedNumber = $number ?? (\App\Domains\Company\Support\ShareCompany::contact(
        app(\App\Domains\Company\Services\CompanyService::class)->profile(),
        config('zyntech-media.contact', []),
    )['whatsapp'] ?? '');
    $digits = preg_replace('/\D+/', '', (string) $resolvedNumber);
@endphp

{{-- No-op until a WhatsApp number is set on the Company profile — see docs/INVOICING.md. --}}
@if ($digits !== '')
    <a
        href="https://wa.me/{{ $digits }}?text={{ rawurlencode($message) }}"
        target="_blank"
        rel="noopener noreferrer"
        {{ $attributes }}
    >{{ $slot }}</a>
@endif
