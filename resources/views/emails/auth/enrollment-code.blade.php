@extends('emails.layouts.horizon', [
    'intent' => 'security',
    'eyebrow' => 'Two-factor setup',
    'heading' => 'Lock in your second factor.',
    'preheader' => 'Your Zytech 2FA setup code is '.$code.'. It expires in 10 minutes.',
])

@section('content')
    @include('emails.partials.copy', [
        'text' => 'Hi '.e($userName).' — enter this code to confirm email as a two-factor method on your Zytech account.',
    ])

    @include('emails.partials.otp', ['code' => $code])

    @include('emails.partials.copy', [
        'text' => 'This code expires in <strong>10 minutes</strong>. If you did not start 2FA setup, you can ignore this email.',
    ])

    @include('emails.partials.alert', [
        'tone' => 'info',
        'title' => 'Almost done',
        'body' => 'After this, sign-ins will ask for a code whenever you enable email 2FA.',
    ])
@endsection
