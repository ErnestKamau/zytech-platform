@extends('emails.layouts.horizon', [
    'intent' => 'security',
    'eyebrow' => 'Sign-in code',
    'heading' => 'Confirm it’s you.',
    'preheader' => 'Your Zytech sign-in code is '.$code.'. It expires in 10 minutes.',
])

@section('content')
    @include('emails.partials.copy', [
        'text' => 'Hi '.e($userName).' — use this one-time code to finish signing in to your Zytech portal.',
    ])

    @include('emails.partials.otp', ['code' => $code])

    @include('emails.partials.copy', [
        'text' => 'This code expires in <strong>10 minutes</strong>. If you did not try to sign in, ignore this email and consider updating your password.',
    ])

    @include('emails.partials.alert', [
        'tone' => 'warning',
        'title' => 'Wasn’t you?',
        'body' => 'Someone may have your password. Change it from Account → Security after you sign in safely.',
    ])
@endsection
