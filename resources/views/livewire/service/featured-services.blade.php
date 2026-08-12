<div>
    @if ($services->isNotEmpty())
        <section class="zy-section zy-section--alt">
            <div class="zy-container">
                <div class="zy-section__header">
                    <p class="zy-section__eyebrow">Featured</p>
                    <h2>Services principals ask for first</h2>
                </div>
                <div class="zy-grid zy-grid--3">
                    @foreach ($services as $service)
                        <x-services.card :service="$service" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</div>
