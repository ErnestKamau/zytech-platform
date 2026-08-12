<?php

namespace App\Domains\Media\Services;

use App\Core\Enums\MediaType;
use App\Core\Services\BaseService;
use App\Models\Media;

final class VideoService extends BaseService
{
    /**
     * Video transcoding waits for FFmpeg in a later phase.
     *
     * @return array{duration: null, poster: string|null}
     */
    public function metadata(Media $media): array
    {
        if ($media->mediaType() !== MediaType::Video) {
            return [
                'duration' => null,
                'poster' => null,
            ];
        }

        $poster = $media->getCustomProperty('poster_path');

        return [
            'duration' => null,
            'poster' => is_string($poster) && $poster !== '' ? $poster : null,
        ];
    }
}
