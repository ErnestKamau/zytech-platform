@extends('emails.layouts.horizon', [
    'intent' => 'brand',
    'eyebrow' => 'Staff invite',
    'heading' => 'Your Admin console is ready.',
    'preheader' => 'Sign in to Zytech Admin to manage clients, quotations, and projects.',
])

@section('content')
    @include('emails.partials.copy', [
        'text' => 'Hi '.e($userName).' — you’ve been invited to the <strong>Zytech Operations Console</strong> (Admin). This is for staff: clients, quotations, orders, projects, and the team — not the client portal.',
    ])

    @include('emails.partials.cta', [
        'url' => $inviteUrl,
        'label' => 'Open Admin sign in',
    ])

    @include('emails.partials.copy', [
        'text' => 'Use the email and password your administrator set for you. After your first sign-in you’ll get a short onboarding that explains Admin and how to return from the website (shield icon when signed in).',
    ])

    @include('emails.partials.alert', [
        'tone' => 'info',
        'title' => 'Bookmark tip',
        'body' => 'You can always open Admin at /admin. On the public site, the shield icon appears only for staff accounts.',
    ])
@endsection
