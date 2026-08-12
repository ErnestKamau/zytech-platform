<div>
    @if ($articles->isNotEmpty())
        <section class="zy-section zy-section--alt">
            <div class="zy-container">
                <div class="zy-section__header">
                    <p class="zy-section__eyebrow">Featured</p>
                    <h2>Guides from the field</h2>
                </div>
                <div class="zy-grid zy-grid--3">
                    @foreach ($articles as $article)
                        <x-knowledge.card :article="$article" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</div>
