@extends('layouts.website')

@section('title', 'Privacy Policy — Zytech Contractors')
@section('page-class', 'zy-page zy-page-legal')

@section('content')
    <article class="zy-section">
        <div class="zy-container zy-legal">
            <p class="zy-section__eyebrow">Legal</p>
            <h1>Privacy Policy</h1>
            <p class="zy-muted">Last updated {{ now()->toFormattedDateString() }}</p>

            <h2>Who we are</h2>
            <p>Zytech Contractors (“we”, “us”) operates the Zytech platform, including our public website and client portal. This policy explains how we collect and use personal information.</p>

            <h2>Information we collect</h2>
            <ul>
                <li>Contact details you submit (name, email, phone) via quotation or contact forms</li>
                <li>Account details if you register for portal access</li>
                <li>Project and quotation records linked to your client profile</li>
                <li>Technical data such as IP address, browser type, and session identifiers</li>
            </ul>

            <h2>How we use information</h2>
            <ul>
                <li>To respond to enquiries and prepare quotations</li>
                <li>To deliver project updates, documents, and portal notifications</li>
                <li>To improve site performance, security, and support</li>
                <li>To meet legal and contractual obligations</li>
            </ul>

            <h2>Sharing</h2>
            <p>We do not sell personal data. We may share information with trusted processors (for example email delivery via Resend, hosting, or analytics) under agreements that protect your data, or when required by law.</p>

            <h2>Retention</h2>
            <p>We keep client and project records for as long as needed to deliver services and meet statutory requirements, then archive or delete them according to our retention schedule.</p>

            <h2>Your choices</h2>
            <p>You may request access, correction, or deletion of personal data where applicable. Contact us at the email listed on our <a href="{{ route('contact') }}">contact page</a>.</p>

            <h2>Security</h2>
            <p>We use access controls, encrypted transport (HTTPS), and activity logging on sensitive actions. No method of transmission is 100% secure; please use strong passwords for portal accounts.</p>
        </div>
    </article>
@endsection
