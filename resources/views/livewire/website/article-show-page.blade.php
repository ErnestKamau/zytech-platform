@php
    $images = config('zyntech-media.images');
    $image = $article->imageKey && isset($images[$article->imageKey])
        ? $images[$article->imageKey]
        : $images['commercial_courtyard'];
@endphp

<div>
    <div class="zy-container zy-knowledge-intro">
        <x-media.banner :src="asset($image['path'])" :alt="$image['alt']">
            <p class="zy-eyebrow" style="color: rgb(255 255 255 / 0.75);">{{ $article->categoryName }}</p>
            <h1 style="color: #fff;">{{ $article->title }}</h1>
            <p>{{ $article->excerpt }}</p>
        </x-media.banner>
    </div>

    <section class="zy-section">
        <div class="zy-container zy-article-detail">
            <div class="zy-article-detail__meta">
                <p class="zy-card__eyebrow">{{ $article->type->label() }}</p>
                <p class="zy-card__body">
                    By {{ $article->authorName }}
                    · {{ $article->readingTimeMinutes }} min read
                    · {{ $article->readingLevel->label() }}
                </p>
                @if (count($article->tags) > 0)
                    <ul class="zy-article-tags">
                        @foreach ($article->tags as $tag)
                            <li>{{ $tag }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="zy-article-detail__body">
                @forelse ($article->sections as $section)
                    <article class="zy-article-section">
                        @if ($section->heading !== '')
                            <h2>{{ $section->heading }}</h2>
                        @endif
                        @if ($section->imageKey && isset($images[$section->imageKey]))
                            <x-media.cover
                                :src="asset($images[$section->imageKey]['path'])"
                                :alt="$images[$section->imageKey]['alt']"
                            />
                        @endif
                        <div>{!! nl2br(e($section->body)) !!}</div>
                    </article>
                @empty
                    <p>{{ $article->excerpt }}</p>
                @endforelse
            </div>
        </div>
    </section>

    @if ($downloads->isNotEmpty())
        <section class="zy-section zy-section--alt">
            <div class="zy-container">
                <div class="zy-section__header">
                    <p class="zy-section__eyebrow">Downloads</p>
                    <h2>Resources</h2>
                </div>
                <div class="zy-grid zy-grid--2">
                    @foreach ($downloads as $download)
                        <x-ui.card>
                            <p class="zy-card__title">{{ $download->title }}</p>
                            @if ($download->description)
                                <p class="zy-card__body">{{ $download->description }}</p>
                            @endif
                            @if ($download->external_url)
                                <a href="{{ $download->external_url }}" class="zy-btn zy-btn--secondary zy-btn--sm" target="_blank" rel="noopener">Download</a>
                            @endif
                        </x-ui.card>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($linkedProjects->isNotEmpty())
        <section class="zy-section">
            <div class="zy-container">
                <div class="zy-section__header">
                    <p class="zy-section__eyebrow">Related projects</p>
                    <h2>See this topic on site</h2>
                </div>
                <div class="zy-grid zy-grid--3">
                    @foreach ($linkedProjects as $project)
                        <x-projects.card :project="$project" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($linkedServices->isNotEmpty())
        <section class="zy-section zy-section--alt">
            <div class="zy-container">
                <div class="zy-section__header">
                    <p class="zy-section__eyebrow">Related services</p>
                    <h2>Work with Zytech on this topic</h2>
                </div>
                <div class="zy-grid zy-grid--3">
                    @foreach ($linkedServices as $service)
                        <x-services.card :service="$service" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <livewire:knowledge.article-faqs :article-id="$model->id" />

    <livewire:knowledge.related-articles :article-id="$model->id" />

    <x-sections.cta>
        <a href="{{ route('contact') }}" class="zy-btn zy-btn--primary zy-btn--lg">Request a Quote</a>
        <a href="{{ route('knowledge.index') }}" class="zy-btn zy-btn--frost zy-btn--lg">All articles</a>
    </x-sections.cta>
</div>
