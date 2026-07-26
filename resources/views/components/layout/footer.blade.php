<footer class="zy-footer">
    <div class="zy-container zy-footer__inner">
        <div>
            <p class="zy-footer__brand">Zytech <span>Contractors</span></p>
            <p class="zy-footer__copy">Precision-built spaces for residential and commercial clients across Kenya — from first sketch to final handover.</p>
        </div>
        <div style="display: grid; gap: var(--zy-space-4); justify-items: start;">
            <nav class="zy-footer__links" aria-label="Footer">
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ route('projects.index') }}">Projects</a>
                <a href="{{ route('styleguide') }}">Style Guide</a>
            </nav>
            <p class="zy-footer__meta">&copy; {{ date('Y') }} Zytech Contractors · Nairobi, Kenya</p>
        </div>
    </div>
</footer>
