<header class="zy-header">
    <div class="zy-container zy-header__inner">
        <a href="{{ route('home') }}" class="zy-nav__brand">
            Zytech <span>Contractors</span>
        </a>

        <nav class="zy-nav zy-header__nav" aria-label="Primary">
            <a href="{{ route('home') }}" class="zy-nav__link" @if (request()->routeIs('home')) aria-current="page" @endif>Home</a>
            <a href="{{ route('projects.index') }}" class="zy-nav__link" @if (request()->routeIs('projects.*')) aria-current="page" @endif>Projects</a>
            <a href="{{ route('styleguide') }}" class="zy-nav__link" @if (request()->routeIs('styleguide')) aria-current="page" @endif>Style Guide</a>
        </nav>

        <div class="zy-header__actions">
            <a href="#quote" class="zy-btn zy-btn--gradient zy-btn--sm">Request a Quote</a>
        </div>
    </div>
</header>
