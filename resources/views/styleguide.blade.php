<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Zytech Design System — Style Guide</title>
    @vite(['resources/css/website/app.css', 'resources/js/app.js'])
    <style>
        /* Styleguide-only chrome — deliberately kept OUT of app.css since real
           pages never need it. Fine to inline/scope here. */
        .sg-swatch { border-radius: var(--zy-radius-md); height: 64px; display: flex; align-items: flex-end; padding: var(--zy-space-2); font-size: var(--zy-text-xs); font-weight: 600; }
        .sg-swatch-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(90px, 1fr)); gap: var(--zy-space-2); margin-bottom: var(--zy-space-8); }
        .sg-block { margin-bottom: var(--zy-space-24); }
        .sg-block > h2 { border-bottom: 1px solid var(--zy-color-border); padding-bottom: var(--zy-space-3); margin-bottom: var(--zy-space-8); }
        .sg-row { display: flex; flex-wrap: wrap; gap: var(--zy-space-4); align-items: center; margin-bottom: var(--zy-space-4); }
        .sg-grid-3 { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: var(--zy-space-6); }
        .sg-icon-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(72px, 1fr)); gap: var(--zy-space-4); text-align: center; }
        .sg-icon-grid svg { margin-inline: auto; color: var(--zy-color-primary); }
        .sg-icon-grid span { display: block; font-size: 11px; color: var(--zy-color-muted); margin-top: var(--zy-space-1); }
        .sg-hero-preview { background: var(--zy-gradient-hero); border-radius: var(--zy-radius-lg); padding: var(--zy-space-16); color: white; }
        .sg-dark-strip { background: var(--zy-slate-900); border-radius: var(--zy-radius-lg); padding: var(--zy-space-8); }
        .sg-theme-toggle {
            position: fixed; top: var(--zy-space-4); right: var(--zy-space-4); z-index: 60;
            box-shadow: var(--zy-shadow-md);
        }
    </style>
    <script>
        /* Apply saved theme before Alpine boots to avoid a light-mode flash. */
        document.documentElement.dataset.zyTheme = localStorage.getItem('zy-theme') || 'light';
    </script>
</head>
<body
    x-data="{ dark: localStorage.getItem('zy-theme') === 'dark' }"
    x-bind:data-zy-theme="dark ? 'dark' : 'light'"
    x-effect="
        localStorage.setItem('zy-theme', dark ? 'dark' : 'light');
        document.documentElement.dataset.zyTheme = dark ? 'dark' : 'light';
    "
>

<x-ui.theme-toggle class="sg-theme-toggle" />

<div class="zy-container zy-container--wide" style="padding-block: var(--zy-space-16);">

    <p class="zy-eyebrow">Internal / Not Public</p>
    <h1>Zytech Design System</h1>
    <p style="max-width: 60ch; color: var(--zy-color-muted); margin-top: var(--zy-space-2);">
        Live reference for every token and component. If something on the real site doesn't
        match what's shown here, the site is wrong — this page is the source of truth.
        Toggle the moon/sun control (top right) to audit every component in dark mode.
    </p>

    {{-- ============================================================ HERO / GRADIENT ============================================================ --}}
    <section class="sg-block" style="margin-top: var(--zy-space-16);">
        <h2>Hero Gradient</h2>
        <div class="sg-hero-preview">
            <p class="zy-eyebrow" style="color: rgba(255,255,255,0.85);">Interior · Exterior · Structural</p>
            <h1 style="color: white; max-width: 18ch;">Precision-built spaces, engineered to last.</h1>
        </div>
    </section>

    {{-- ============================================================ COLOR ============================================================ --}}
    <section class="sg-block">
        <h2>Color Palette</h2>

        <h4 style="margin-bottom: var(--zy-space-3);">Sky — Primary</h4>
        <div class="sg-swatch-row">
            @foreach ([50,100,200,300,400,500,600,700,800,900] as $step)
                <div class="sg-swatch" style="background: var(--zy-sky-{{ $step }}); color: {{ $step >= 500 ? '#fff' : 'var(--zy-color-ink)' }};">{{ $step }}</div>
            @endforeach
        </div>

        <h4 style="margin-bottom: var(--zy-space-3);">Indigo — Secondary</h4>
        <div class="sg-swatch-row">
            @foreach ([50,100,200,300,400,500,600,700,800,900] as $step)
                <div class="sg-swatch" style="background: var(--zy-indigo-{{ $step }}); color: {{ $step >= 500 ? '#fff' : 'var(--zy-color-ink)' }};">{{ $step }}</div>
            @endforeach
        </div>

        <h4 style="margin-bottom: var(--zy-space-3);">Slate — Neutrals</h4>
        <div class="sg-swatch-row">
            @foreach ([50,100,200,300,400,500,600,700,800,900] as $step)
                <div class="sg-swatch" style="background: var(--zy-slate-{{ $step }}); color: {{ $step >= 500 ? '#fff' : 'var(--zy-color-ink)' }};">{{ $step }}</div>
            @endforeach
        </div>

        <h4 style="margin-bottom: var(--zy-space-3);">Semantic States</h4>
        <div class="sg-grid-3">
            @foreach (['success' => 'Success', 'danger' => 'Danger', 'warning' => 'Warning', 'info' => 'Info'] as $key => $label)
                <div>
                    <p class="zy-text-sm" style="margin-bottom: var(--zy-space-2);">{{ $label }}</p>
                    <div class="sg-swatch-row" style="margin-bottom: 0;">
                        <div class="sg-swatch" style="background: var(--zy-{{ $key }}-50); color: var(--zy-{{ $key }}-700);">50</div>
                        <div class="sg-swatch" style="background: var(--zy-{{ $key }}-300); color: #fff;">300</div>
                        <div class="sg-swatch" style="background: var(--zy-{{ $key }}-500); color: #fff;">500</div>
                        <div class="sg-swatch" style="background: var(--zy-{{ $key }}-700); color: #fff;">700</div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ============================================================ TYPE ============================================================ --}}
    <section class="sg-block">
        <h2>Typography</h2>
        <div class="zy-stack">
            <h1>H1 — Precision-built spaces</h1>
            <h2>H2 — Major section heading</h2>
            <h3>H3 — Subsection heading</h3>
            <h4>H4 — Card / minor heading</h4>
            <p>Body text at 16px minimum, 1.6 line-height, max 65ch reading width. Zytech plans, designs, and delivers residential and commercial projects across Kenya — from first sketch to final handover.</p>
            <p class="zy-text-sm">Small text — captions, meta info, timestamps.</p>
            <p><span class="zy-text-gradient" style="font-weight:800; font-size: var(--zy-text-2xl);">Gradient text</span> — reserve for ONE hero moment per page.</p>
        </div>
    </section>

    {{-- ============================================================ SPACING ============================================================ --}}
    <section class="sg-block">
        <h2>Spacing (8pt scale)</h2>
        <div class="zy-stack">
            @foreach ([1,2,3,4,6,8,10,12,16,20,24] as $step)
                <div class="sg-row">
                    <div style="width: var(--zy-space-{{ $step }}); height: 20px; background: var(--zy-color-primary); border-radius: 3px;"></div>
                    <span class="zy-text-sm">--zy-space-{{ $step }} ({{ ['1'=>4,'2'=>8,'3'=>12,'4'=>16,'6'=>24,'8'=>32,'10'=>40,'12'=>48,'16'=>64,'20'=>80,'24'=>96][$step] }}px)</span>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ============================================================ BUTTONS ============================================================ --}}
    <section class="sg-block">
        <h2>Buttons</h2>

        <p class="zy-text-sm" style="margin-bottom: var(--zy-space-3);">Variants</p>
        <div class="sg-row">
            <x-ui.button variant="primary">Primary</x-ui.button>
            <x-ui.button variant="gradient">Gradient CTA</x-ui.button>
            <x-ui.button variant="secondary">Secondary</x-ui.button>
            <x-ui.button variant="ghost">Ghost</x-ui.button>
            <x-ui.button variant="success">Success</x-ui.button>
            <x-ui.button variant="danger">Danger</x-ui.button>
            <x-ui.button variant="warning">Warning</x-ui.button>
        </div>

        <p class="zy-text-sm" style="margin: var(--zy-space-6) 0 var(--zy-space-3);">States</p>
        <div class="sg-row">
            <x-ui.button variant="primary">Default</x-ui.button>
            <x-ui.button variant="primary" style="background: var(--zy-color-primary-hover);">Hover (forced)</x-ui.button>
            <x-ui.button variant="primary" disabled>Disabled</x-ui.button>
            <x-ui.button variant="primary" class="zy-btn--loading">Loading</x-ui.button>
        </div>

        <p class="zy-text-sm" style="margin: var(--zy-space-6) 0 var(--zy-space-3);">Sizes</p>
        <div class="sg-row">
            <x-ui.button variant="primary" size="sm">Small</x-ui.button>
            <x-ui.button variant="primary">Base</x-ui.button>
            <x-ui.button variant="primary" size="lg">Large</x-ui.button>
        </div>
    </section>

    {{-- ============================================================ ALERTS + BADGES ============================================================ --}}
    <section class="sg-block">
        <h2>Alerts &amp; Badges</h2>

        <div class="zy-stack" style="margin-bottom: var(--zy-space-8);">
            <x-ui.alert type="info" title="Heads up">Your quote request is being reviewed by our estimation team.</x-ui.alert>
            <x-ui.alert type="success" title="Submitted">Your quote request was received. We'll respond within 48 hours.</x-ui.alert>
            <x-ui.alert type="warning" title="Missing information">Please attach a site plan before submitting.</x-ui.alert>
            <x-ui.alert type="danger" title="Upload failed">That file exceeds the 20MB limit.</x-ui.alert>
        </div>

        <div class="sg-row">
            <x-ui.badge variant="neutral">Neutral</x-ui.badge>
            <x-ui.badge variant="primary">Primary</x-ui.badge>
            <x-ui.badge variant="success">Approved</x-ui.badge>
            <x-ui.badge variant="danger">Rejected</x-ui.badge>
            <x-ui.badge variant="warning">Pending</x-ui.badge>
            <x-ui.badge variant="gradient">Featured</x-ui.badge>
        </div>

        <div class="sg-row" style="margin-top: var(--zy-space-4);">
            <span class="zy-status zy-status--success">Approved</span>
            <span class="zy-status zy-status--warning">In Review</span>
            <span class="zy-status zy-status--danger">Rejected</span>
            <span class="zy-status zy-status--info">Draft</span>
            <span class="zy-status zy-status--neutral">Archived</span>
        </div>
    </section>

    {{-- ============================================================ CARDS ============================================================ --}}
    <section class="sg-block">
        <h2>Cards</h2>
        <div class="sg-grid-3">
            <x-ui.card interactive>
                <p class="zy-card__eyebrow">Residential</p>
                <p class="zy-card__title">Kilimani Modern Villa</p>
                <p class="zy-card__body">Completed 2026 · Nairobi</p>
            </x-ui.card>

            <x-ui.card class="zy-card--stat">
                <p class="zy-stat__value">120+</p>
                <p class="zy-stat__label">Projects delivered</p>
            </x-ui.card>

            <x-ui.card featured>
                <p class="zy-card__eyebrow">Featured</p>
                <p class="zy-card__title">Two Rivers Office Park</p>
                <p class="zy-card__body">Our largest commercial build to date.</p>
            </x-ui.card>
        </div>
    </section>

    {{-- ============================================================ FORMS ============================================================ --}}
    <section class="sg-block">
        <h2>Forms</h2>
        <div class="sg-grid-3">
            <div class="zy-field">
                <label class="zy-label">Full name <span class="zy-label__required">*</span></label>
                <input class="zy-input" placeholder="Jane Wanjiru">
            </div>

            <div class="zy-field zy-field--error">
                <label class="zy-label">Email</label>
                <input class="zy-input" value="not-an-email">
                <p class="zy-field__message zy-field__message--error">Enter a valid email address.</p>
            </div>

            <div class="zy-field zy-field--success">
                <label class="zy-label">Phone</label>
                <input class="zy-input" value="+254 700 000 000">
                <p class="zy-field__message zy-field__message--success">Looks good.</p>
            </div>
        </div>

        <div class="sg-row" style="margin-top: var(--zy-space-4);">
            <label class="zy-checkbox"><input type="checkbox" checked> Send me updates</label>
            <label class="zy-radio"><input type="radio" name="sg-r" checked> Residential</label>
            <label class="zy-radio"><input type="radio" name="sg-r"> Commercial</label>
            <label class="zy-switch"><input type="checkbox" checked><span class="zy-switch__track"></span></label>
        </div>

        <div class="zy-dropzone" style="margin-top: var(--zy-space-6);">Drop plans, PDFs, or images here</div>
    </section>

    {{-- ============================================================ TABS ============================================================ --}}
    <section class="sg-block">
        <h2>Tabs</h2>
        <x-ui.tabs :tabs="['overview' => 'Overview', 'gallery' => 'Gallery', 'timeline' => 'Timeline']" default="overview">
            <x-slot:overview><p>Project overview content goes here.</p></x-slot:overview>
            <x-slot:gallery><p>Gallery grid goes here.</p></x-slot:gallery>
            <x-slot:timeline><p>Construction timeline goes here.</p></x-slot:timeline>
        </x-ui.tabs>
    </section>

    {{-- ============================================================ DROPDOWN ============================================================ --}}
    <section class="sg-block">
        <h2>Dropdown</h2>
        <x-ui.dropdown label="Project actions">
            <button class="zy-dropdown__item">Edit project</button>
            <button class="zy-dropdown__item">Duplicate</button>
            <div class="zy-dropdown__divider"></div>
            <button class="zy-dropdown__item zy-dropdown__item--danger">Delete</button>
        </x-ui.dropdown>
    </section>

    {{-- ============================================================ WIZARD ============================================================ --}}
    <section class="sg-block">
        <h2>Wizard / Stepper</h2>
        <x-ui.wizard-steps :steps="['Project Details', 'Upload Plans', 'Contact Info', 'Review']" :current="2" />
    </section>

    {{-- ============================================================ TABLE ============================================================ --}}
    <section class="sg-block">
        <h2>Table</h2>
        <div class="zy-table-wrap">
            <table class="zy-table zy-table--striped">
                <thead>
                    <tr><th>Project</th><th>Client</th><th>Status</th><th>Value</th></tr>
                </thead>
                <tbody>
                    <tr><td>Kilimani Villa</td><td>Private</td><td><span class="zy-status zy-status--success">Completed</span></td><td>KES 18.4M</td></tr>
                    <tr><td>Two Rivers Office</td><td>Corporate</td><td><span class="zy-status zy-status--warning">In Progress</span></td><td>KES 210M</td></tr>
                    <tr><td>Karen Renovation</td><td>Private</td><td><span class="zy-status zy-status--info">Planning</span></td><td>KES 6.2M</td></tr>
                </tbody>
            </table>
        </div>
    </section>

    {{-- ============================================================ MODAL ============================================================ --}}
    <section class="sg-block" x-data="{ sgModal: false }">
        <h2>Modal</h2>
        <button class="zy-btn zy-btn--primary" x-on:click="sgModal = true">Open modal</button>

        <x-ui.modal show="sgModal" title="Request a Quote">
            <div class="zy-field">
                <label class="zy-label">Project type</label>
                <select class="zy-select">
                    <option>Residential</option>
                    <option>Commercial</option>
                </select>
            </div>
            <x-slot:footer>
                <button class="zy-btn zy-btn--secondary" x-on:click="sgModal = false">Cancel</button>
                <button class="zy-btn zy-btn--primary">Submit</button>
            </x-slot:footer>
        </x-ui.modal>
    </section>

    {{-- ============================================================ ICON TILES + ICON BUTTONS ============================================================ --}}
    <section class="sg-block">
        <h2>Icon Tiles &amp; Icon Buttons</h2>

        <p class="zy-text-sm" style="margin-bottom: var(--zy-space-3);">Tiles — sm / default / lg / gradient (one per view) / slate / ring</p>
        <div class="sg-row">
            @php
                $sgTileIcon = 'M2.25 12l8.954-8.955a1.5 1.5 0 012.122 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75';
            @endphp
            @foreach (['zy-icon-tile--sm', '', 'zy-icon-tile--lg', 'zy-icon-tile--gradient', 'zy-icon-tile--slate', 'zy-icon-tile--ring'] as $tileVariant)
                <span class="zy-icon-tile {{ $tileVariant }}">
                    <svg class="zy-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $sgTileIcon }}" />
                    </svg>
                </span>
            @endforeach
        </div>

        <p class="zy-text-sm" style="margin: var(--zy-space-6) 0 var(--zy-space-3);">Icon-only buttons — ghost / frost (on dark)</p>
        <div class="sg-row">
            <button type="button" class="zy-icon-btn" aria-label="Close">
                <svg class="zy-icon zy-icon--sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <span class="sg-dark-strip" style="padding: var(--zy-space-3);">
                <button type="button" class="zy-icon-btn zy-icon-btn--frost" aria-label="Settings">
                    <svg class="zy-icon zy-icon--sm zy-icon--spin-hover" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12a7.5 7.5 0 0015 0m-15 0a7.5 7.5 0 1115 0m-15 0H3m16.5 0H21m-1.5 0H12m-8.457 3.077l1.41-.513m14.095-5.13l1.41-.513M5.106 17.785l1.15-.964m11.49-9.642l1.149-.964M7.501 19.795l.75-1.3m7.5-12.99l.75-1.3m-6.063 16.658l.26-1.477m2.605-14.772l.26-1.477m0 17.726l-.26-1.477M10.698 4.614l-.26-1.477M16.5 19.794l-.75-1.299M7.5 4.205L12 12m6.894 5.785l-1.149-.964M6.256 7.178l-1.15-.964m15.352 8.864l-1.41-.513M4.954 9.435l-1.41-.514M12.002 12l-3.75 6.495" />
                    </svg>
                </button>
            </span>
        </div>

        <p class="zy-text-sm" style="margin: var(--zy-space-6) 0 var(--zy-space-3);">Frost buttons — for CTAs on dark / photographic surfaces</p>
        <div class="sg-dark-strip sg-row" style="margin-bottom: 0;">
            <button class="zy-btn zy-btn--gradient">Request a Quote</button>
            <button class="zy-btn zy-btn--frost">View Projects</button>
        </div>
    </section>

    {{-- ============================================================ ICONS ============================================================ --}}
    <section class="sg-block">
        <h2>Iconography (Heroicons, per UI_UX.md)</h2>
        <p class="zy-text-sm" style="margin-bottom: var(--zy-space-6);">
            Pull actual SVGs from <a class="zy-link" href="https://heroicons.com" target="_blank">heroicons.com</a> —
            these are stand-ins showing sizing/color consistency (24px, stroke-width 1.5, inherits currentColor).
        </p>
        <div class="sg-icon-grid">
            @foreach ([
                'Home' => 'M2.25 12l8.954-8.955a1.5 1.5 0 012.122 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75',
                'Doc' => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z',
                'Camera' => 'M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.822 1.316z',
                'Check' => 'M4.5 12.75l6 6 9-13.5',
                'Chart' => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z',
                'Shield' => 'M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.75h-.152c-3.196 0-6.1-1.248-8.25-3.286z',
            ] as $name => $path)
                <div>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="{{ $path }}" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>{{ $name }}</span>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ============================================================ RADIUS + SHADOW ============================================================ --}}
    <section class="sg-block">
        <h2>Radius &amp; Shadow</h2>
        <div class="sg-row">
            <div style="width:80px;height:80px;background:var(--zy-color-primary);border-radius:var(--zy-radius-sm);"></div>
            <div style="width:80px;height:80px;background:var(--zy-color-primary);border-radius:var(--zy-radius-md);"></div>
            <div style="width:80px;height:80px;background:var(--zy-color-primary);border-radius:var(--zy-radius-lg);"></div>
            <div style="width:80px;height:80px;background:var(--zy-color-primary);border-radius:var(--zy-radius-full);"></div>
        </div>
        <div class="sg-row" style="margin-top: var(--zy-space-6);">
            <div style="width:100px;height:70px;background:var(--zy-color-surface);border:1px solid var(--zy-color-border);border-radius:var(--zy-radius-md);box-shadow:var(--zy-shadow-sm);"></div>
            <div style="width:100px;height:70px;background:var(--zy-color-surface);border:1px solid var(--zy-color-border);border-radius:var(--zy-radius-md);box-shadow:var(--zy-shadow-md);"></div>
            <div style="width:100px;height:70px;background:var(--zy-color-surface);border:1px solid var(--zy-color-border);border-radius:var(--zy-radius-md);box-shadow:var(--zy-shadow-lg);"></div>
        </div>
    </section>

</div>
</body>
</html>
