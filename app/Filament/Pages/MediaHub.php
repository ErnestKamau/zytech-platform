<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Media\MediaResource;
use App\Filament\Resources\MediaFolders\MediaFolderResource;
use App\Filament\Resources\MediaTags\MediaTagResource;
use Filament\Support\Icons\Heroicon;

class MediaHub extends AdminHubPage
{
    protected static ?string $navigationLabel = 'Media';

    protected static ?string $title = 'Media';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?int $navigationSort = 10;

    protected function getHubSubheading(): ?string
    {
        return 'Library, folders, and tags.';
    }

    public function getHubSections(): array
    {
        return [
            [
                'title' => 'Library',
                'cards' => [
                    $this->hubCard('Media', MediaResource::class, Heroicon::OutlinedPhoto),
                    $this->hubCard('Folders', MediaFolderResource::class, Heroicon::OutlinedFolder),
                    $this->hubCard('Tags', MediaTagResource::class, Heroicon::OutlinedTag),
                ],
            ],
        ];
    }
}
