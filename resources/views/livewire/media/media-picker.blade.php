<div class="zy-media-picker">
    <input
        type="search"
        class="zy-input"
        wire:model.live.debounce.300ms="query"
        placeholder="Search media"
    >

    <ul class="zy-media-picker__list">
        @forelse ($items as $item)
            <li>
                <span>{{ $item->name }}</span>
                <small>{{ $item->mime_type }}</small>
            </li>
        @empty
            <li>No media found.</li>
        @endforelse
    </ul>
</div>
