@props([
    'context' => 'login', // login|register
])

<div class="zy-auth-social">
    <div class="zy-auth-social__divider" aria-hidden="true">
        <span>or continue with</span>
    </div>

    <button
        type="button"
        class="zy-auth-social__btn zy-auth-social__btn--google"
        disabled
        aria-disabled="true"
        title="Google sign-in coming soon"
    >
        <svg class="zy-auth-social__google-icon" viewBox="0 0 24 24" aria-hidden="true">
            <path fill="#EA4335" d="M12 10.2v3.6h5.1c-.2 1.2-.9 2.3-1.9 3l3.1 2.4c1.8-1.7 2.9-4.1 2.9-7 0-.7-.1-1.3-.2-1.9H12z"/>
            <path fill="#34A853" d="M6.6 14.3l-.9.7-2.6 2c1.7 3.3 5.1 5.5 9 5.5 2.7 0 5-.9 6.6-2.4l-3.1-2.4c-.9.6-2 1-3.5 1-2.7 0-5-1.8-5.8-4.3z"/>
            <path fill="#4A90E2" d="M3.1 7.1C2.4 8.4 2 9.9 2 11.5s.4 3.1 1.1 4.4l3.5-2.7c-.2-.6-.3-1.2-.3-1.7 0-.6.1-1.1.3-1.7L3.1 7.1z"/>
            <path fill="#FBBC05" d="M12 4.8c1.5 0 2.8.5 3.8 1.5l2.8-2.8C16.9 1.8 14.7 1 12 1 8.1 1 4.7 3.2 3.1 6.5l3.5 2.7C7 6.6 9.3 4.8 12 4.8z"/>
        </svg>
        <span>Continue with Google</span>
    </button>

    <p class="zy-auth-social__note">Google sign-in will be available soon.</p>
</div>
