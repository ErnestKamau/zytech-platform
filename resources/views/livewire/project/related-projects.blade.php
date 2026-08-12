<div>
    @if ($projects->isNotEmpty())
        <section class="zy-section">
            <div class="zy-container">
                <div class="zy-section__header">
                    <p class="zy-section__eyebrow">Related</p>
                    <h2>More work in this discipline</h2>
                </div>
                <div class="zy-grid zy-grid--3">
                    @foreach ($projects as $project)
                        <x-projects.card :project="$project" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</div>
