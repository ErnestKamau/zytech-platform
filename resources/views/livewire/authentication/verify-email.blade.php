<div class="zy-auth-form">
    <h1 class="zy-auth-form__title">Verify your email</h1>
    <p class="zy-auth-form__lead">
        We sent a verification link to your inbox. Click it to activate full account access.
    </p>

    @if (session('status'))
        <div class="zy-alert zy-alert--success" role="status">{{ session('status') }}</div>
    @endif

    <form wire:submit="resend" class="zy-stack">
        <button type="submit" class="zy-btn zy-btn--primary zy-btn--lg">Resend verification email</button>
    </form>

    <p class="zy-auth-form__footer">
        <a href="{{ route('account.profile') }}" class="zy-auth-form__link">Continue to account</a>
    </p>
</div>
