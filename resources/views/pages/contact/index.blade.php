@extends('layouts.website')

@php
    $contact = \App\Domains\Company\Support\ShareCompany::contact(
        $companyProfile ?? null,
        $platform['contact'] ?? config('zyntech-media.contact'),
    );
@endphp

@section('title', 'Contact — '.($companyProfile->name ?? 'Zytech Contractors'))
@section('page-class', 'zy-page-contact')

@section('content')
    <section class="zy-contact">
        <p class="zy-contact__watermark" aria-hidden="true">CONTACT</p>

        <div class="zy-container zy-contact__inner">
            <div class="zy-contact__info">
                <p class="zy-contact__badge">
                    <svg class="zy-icon zy-icon--sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                    </svg>
                    Contact
                </p>
                <h1>Get in touch.</h1>
                <p>Have a site in mind, or ready to start a build in Nairobi, Kiambu, or beyond?</p>

                <ul class="zy-contact__cards">
                    <li>
                        <a class="zy-contact-card" href="mailto:{{ $contact['email'] }}">
                            <span class="zy-icon-tile zy-icon-tile--sm" aria-hidden="true">
                                <svg class="zy-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                </svg>
                            </span>
                            <span>
                                <strong>Email us</strong>
                                <span>{{ $contact['email'] }}</span>
                            </span>
                            <svg class="zy-icon zy-icon--sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25" />
                            </svg>
                        </a>
                    </li>
                    <li>
                        <a class="zy-contact-card" href="tel:{{ preg_replace('/\s+/', '', $contact['phone']) }}">
                            <span class="zy-icon-tile zy-icon-tile--sm" aria-hidden="true">
                                <svg class="zy-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                </svg>
                            </span>
                            <span>
                                <strong>Call us</strong>
                                <span>{{ $contact['phone'] }}</span>
                            </span>
                            <svg class="zy-icon zy-icon--sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25" />
                            </svg>
                        </a>
                    </li>
                    <li>
                        <div class="zy-contact-card">
                            <span class="zy-icon-tile zy-icon-tile--sm" aria-hidden="true">
                                <svg class="zy-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                </svg>
                            </span>
                            <span>
                                <strong>Our location</strong>
                                <span>{{ $contact['location'] }} — {{ $contact['service_area'] }}</span>
                            </span>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="zy-contact__panel">
                <livewire:website.contact-form />
            </div>
        </div>
    </section>
@endsection
