@php
    use Filament\Support\Enums\Width;

    $livewire ??= null;
    $renderHookScopes = $livewire?->getRenderHookScopes();
    $maxContentWidth ??= (filament()->getSimplePageMaxContentWidth() ?? Width::Large);

    if (is_string($maxContentWidth)) {
        $maxContentWidth = Width::tryFrom($maxContentWidth) ?? $maxContentWidth;
    }

    $images = config('zyntech-media.images');
    $aside = $images['plan_approvals_desk']
        ?? $images['hero_facade_dusk']
        ?? $images['commercial_courtyard'];
@endphp

<x-filament-panels::layout.base :livewire="$livewire">
    <div class="zy-admin-auth">
        <div class="zy-admin-auth__card">
            <aside class="zy-admin-auth__aside">
                <div class="zy-admin-auth__visual" aria-hidden="true">
                    <div class="zy-admin-auth__media">
                        <img
                            src="{{ asset($aside['path']) }}"
                            alt="{{ $aside['alt'] }}"
                            loading="eager"
                            decoding="async"
                        >
                    </div>
                    <div class="zy-admin-auth__overlay"></div>
                </div>

                <div class="zy-admin-auth__chrome">
                    <a href="{{ url('/') }}" class="zy-admin-auth__brand">
                        <span class="zy-admin-auth__mark" aria-hidden="true">Z</span>
                        <span class="zy-admin-auth__brand-text">Zytech <em>Contractors</em></span>
                    </a>
                    <a href="{{ url('/') }}" class="zy-admin-auth__skip">Website</a>
                </div>

                <div class="zy-admin-auth__body">
                    <p class="zy-admin-auth__eyebrow">Owner &amp; staff access</p>
                    <h2 class="zy-admin-auth__headline">Run the business from one console.</h2>
                    <p class="zy-admin-auth__lead">
                        Quotations, clients, orders, projects, inventory, and communications —
                        the internal workspace for Zytech operations.
                    </p>

                    <ul class="zy-admin-auth__features">
                        <li>Sales pipeline &amp; quotations</li>
                        <li>Orders, invoices &amp; fulfilment</li>
                        <li>Projects, documents &amp; team work</li>
                        <li>Clients &amp; portal access control</li>
                    </ul>
                </div>
            </aside>

            <div class="zy-admin-auth__main-wrap">
                <div class="zy-admin-auth__toolbar">
                    @auth
                        <a href="{{ url('/') }}" class="zy-admin-auth__portal-link">
                            Public website
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="zy-admin-auth__portal-link">
                            Client / account login
                        </a>
                    @endauth
                </div>

                <div class="zy-admin-auth__main-ctn">
                    <main
                        @class([
                            'fi-simple-main',
                            'zy-admin-auth__main',
                            ($maxContentWidth instanceof Width) ? "fi-width-{$maxContentWidth->value}" : $maxContentWidth,
                        ])
                    >
                        {{ $slot }}
                    </main>
                </div>
            </div>
        </div>
    </div>

    {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::FOOTER, scopes: $renderHookScopes) }}
</x-filament-panels::layout.base>
