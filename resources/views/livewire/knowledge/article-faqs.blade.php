<div>
    @if ($faqs->isNotEmpty())
        <section class="zy-section">
            <div class="zy-container">
                <div class="zy-section__header">
                    <p class="zy-section__eyebrow">FAQ</p>
                    <h2>Common questions</h2>
                </div>
                <div class="zy-knowledge-faqs">
                    @foreach ($faqs as $faq)
                        <details class="zy-knowledge-faq">
                            <summary>{{ $faq->question }}</summary>
                            <p>{{ $faq->answer }}</p>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</div>
