@props([
    'brand' => 'Zytech Contractors',
    'headline' => 'Precision-built spaces, engineered to last.',
    'support' => 'Interior, exterior, and structural work — from first sketch to final handover.',
])

<section class="zy-hero">
    <div class="zy-hero__media" aria-hidden="true"></div>
    <div class="zy-container zy-hero__content">
        <p class="zy-eyebrow" style="color: rgba(255,255,255,0.85);">Interior · Exterior · Structural</p>
        <h1 class="zy-hero__brand">{{ $brand }}</h1>
        <p class="zy-hero__headline">{{ $headline }}</p>
        <p class="zy-hero__support">{{ $support }}</p>
        <div class="zy-hero__actions">
            @isset($actions)
                {{ $actions }}
            @else
                <a href="#quote" class="zy-btn zy-btn--gradient zy-btn--lg">Request a Quote</a>
                <a href="{{ route('projects.index') }}" class="zy-btn zy-btn--secondary zy-btn--lg">View Projects</a>
            @endisset
        </div>
    </div>
</section>
