<?php

namespace App\Filament\Resources\Media\Pages;

use App\Core\Enums\MediaCollection;
use App\Domains\Media\Actions\UploadMedia;
use App\Domains\Media\Data\MediaUploadData;
use App\Filament\Resources\Media\MediaResource;
use App\Models\Media;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Storage;

class ManageMedia extends ManageRecords
{
    protected static string $resource = MediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->using(function (array $data): Media {
                    $last = null;

                    foreach ($data['files'] ?? [] as $file) {
                        $path = Storage::disk('public')->path($file);

                        $last = app(UploadMedia::class)->handle(MediaUploadData::fromArray([
                            'folder_id' => $data['folder_id'],
                            'collection' => $data['collection_name'] ?? MediaCollection::Gallery->value,
                            'path' => $path,
                            'name' => pathinfo((string) $file, PATHINFO_FILENAME),
                            'custom_properties' => [
                                'alt' => $data['alt'] ?? '',
                            ],
                        ]));
                    }

                    if ($last === null) {
                        throw new \RuntimeException('Upload at least one file.');
                    }

                    return $last;
                }),
        ];
    }
}
