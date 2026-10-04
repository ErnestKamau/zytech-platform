<?php

namespace App\Domains\Homepage\Actions;

use App\Core\Actions\BaseAction;
use App\Core\Enums\MediaCollection;
use App\Models\HomepageSlide;
use Illuminate\Support\Facades\Storage;

final class SyncHomepageSlideMedia extends BaseAction
{
    public function handle(mixed ...$arguments): void
    {
        /** @var HomepageSlide $slide */
        [$slide, $data] = $arguments;

        $this->attach($slide, $data['media_upload'] ?? null, MediaCollection::HomepageSlideMedia);
        $this->attach($slide, $data['poster_upload'] ?? null, MediaCollection::HomepageSlidePoster);
    }

    private function attach(HomepageSlide $slide, ?string $path, MediaCollection $collection): void
    {
        if ($path === null || $path === '') {
            return;
        }

        $absolutePath = Storage::disk('public')->path($path);

        if (! is_file($absolutePath)) {
            return;
        }

        $slide->addMedia($absolutePath)
            ->usingName($slide->headline ?: 'Homepage slide')
            ->toMediaCollection($collection->value);
    }
}
