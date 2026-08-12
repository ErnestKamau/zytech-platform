<div>
    <div class="zy-container zy-quote-track">
        <p class="zy-section__eyebrow">Track quotation</p>
        <h1>{{ $request->referenceNumber }}</h1>
        <p class="zy-card__body">Submitted for {{ $request->fullName }} · {{ $request->projectType->label() }}</p>

        <div class="zy-quote-status">
            <p class="zy-card__title">Current status</p>
            <p class="zy-quote-status__badge">{{ $request->status->label() }}</p>
        </div>

        <dl class="zy-quote-details">
            <div><dt>County</dt><dd>{{ $request->county ?? '—' }}</dd></div>
            <div><dt>Location</dt><dd>{{ $request->location ?? '—' }}</dd></div>
            <div><dt>Budget</dt><dd>{{ $request->budgetRange?->label() ?? '—' }}</dd></div>
            <div><dt>Timeline</dt><dd>{{ $request->estimatedTimeline ?? '—' }}</dd></div>
            @if (count($request->serviceNames) > 0)
                <div><dt>Services</dt><dd>{{ implode(', ', $request->serviceNames) }}</dd></div>
            @endif
        </dl>

        <p class="zy-card__body">{{ $request->description }}</p>

        <a href="{{ route('quote.index') }}" class="zy-btn zy-btn--secondary">Submit another request</a>
    </div>
</div>
