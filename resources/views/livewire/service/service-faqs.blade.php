<div>
    @if ($faqs->isNotEmpty())
        <section class="zy-section zy-section--alt">
            <div class="zy-container">
                <div class="zy-section__header">
                    <p class="zy-section__eyebrow">FAQs</p>
                    <h2>Questions we hear on this service</h2>
                </div>
                <div class="zy-about-faqs" x-data="{ open: 0 }">
                    @foreach ($faqs as $index => $faq)
                        <div class="zy-about-faq">
                            <button
                                type="button"
                                class="zy-about-faq__q"
                                @click="open = open === {{ $index }} ? null : {{ $index }}"
                                :aria-expanded="open === {{ $index }}"
                            >
                                {{ $faq->question }}
                            </button>
                            <div class="zy-about-faq__a" x-show="open === {{ $index }}" x-cloak>
                                <p>{{ $faq->answer }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</div>
