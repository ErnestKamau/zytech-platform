<div class="zy-admin-onboard">
    <div class="zy-admin-onboard__progress" aria-hidden="true">
        <span @class(['is-active' => $step >= 1])></span>
        <span @class(['is-active' => $step >= 2])></span>
    </div>

    @if ($step === 1)
        <h1 class="zy-admin-onboard__title">What you’re signing into</h1>
        <p class="zy-admin-onboard__lead">
            <strong>Zytech Admin</strong> is the operations console for owners and staff —
            not the client portal.
        </p>

        <ul class="zy-admin-onboard__list">
            <li>Clients, quotations, and the sales pipeline</li>
            <li>Orders, invoices, and fulfilment</li>
            <li>Projects, documents, and team work</li>
            <li>Portal access and staff accounts</li>
        </ul>

        <p class="zy-admin-onboard__tip">
            On the public website, look for the <strong>shield</strong> icon after you sign in —
            it only appears for Admin-capable accounts. You can also bookmark
            <span class="zy-admin-onboard__mono">/admin</span>.
        </p>

        <button type="button" class="fi-btn fi-btn-primary fi-size-md w-full justify-center" wire:click="nextStep">
            Continue
        </button>
    @else
        <h1 class="zy-admin-onboard__title">You’re ready</h1>
        <p class="zy-admin-onboard__lead">
            We’ll keep you signed in on this device for 90 days. Where do you want to go now?
        </p>

        <div class="zy-admin-onboard__actions">
            <button
                type="button"
                class="fi-btn fi-btn-primary fi-size-md w-full justify-center"
                wire:click="finish('admin')"
            >
                Open Admin dashboard
            </button>
            <button
                type="button"
                class="fi-btn fi-btn-color-gray fi-size-md w-full justify-center"
                wire:click="finish('website')"
            >
                Go to public website
            </button>
        </div>

        <button type="button" class="zy-admin-onboard__back" wire:click="previousStep">
            Back
        </button>
    @endif
</div>
