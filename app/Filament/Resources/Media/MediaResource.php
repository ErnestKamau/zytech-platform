<?php

namespace App\Filament\Resources\Media;

use App\Core\Enums\MediaCollection;
use App\Core\Enums\MediaType;
use App\Core\Filament\BaseResource;
use App\Domains\Media\Actions\DeleteMedia;
use App\Filament\Resources\Media\Pages\ManageMedia;
use App\Models\Media;
use App\Models\MediaFolder;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use RuntimeException;

class MediaResource extends BaseResource
{
    protected static ?string $model = Media::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|\UnitEnum|null $navigationGroup = 'Media';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Library';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('folder_id')
                    ->label('Folder')
                    ->options(fn (): array => MediaFolder::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->required()
                    ->searchable()
                    ->visibleOn('create'),
                Select::make('collection_name')
                    ->label('Collection')
                    ->options(collect(MediaCollection::cases())->mapWithKeys(
                        fn (MediaCollection $collection): array => [$collection->value => $collection->label()]
                    ))
                    ->required()
                    ->default(MediaCollection::Gallery->value),
                FileUpload::make('files')
                    ->multiple()
                    ->disk('public')
                    ->directory('media-uploads')
                    ->acceptedFileTypes([
                        'image/jpeg',
                        'image/png',
                        'image/webp',
                        'image/svg+xml',
                        'application/pdf',
                        'video/mp4',
                        'video/quicktime',
                    ])
                    ->maxSize(102400)
                    ->required()
                    ->visibleOn('create'),
                TextInput::make('name')->required()->visibleOn('edit'),
                TextInput::make('alt')
                    ->label('Alt text')
                    ->visibleOn('edit'),
                Select::make('tags')
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->preload()
                    ->visibleOn('edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('preview')
                    ->label('')
                    ->getStateUsing(fn (Media $record): ?string => $record->mediaType() === MediaType::Image ? $record->getUrl() : null),
                TextColumn::make('name')->searchable(),
                TextColumn::make('collection_name')
                    ->badge()
                    ->formatStateUsing(function (string $state): string {
                        $collection = MediaCollection::tryFrom($state);

                        return $collection?->label() ?? $state;
                    }),
                TextColumn::make('mime_type')->toggleable(),
                TextColumn::make('human_readable_size')->label('Size'),
                TextColumn::make('created_at')->dateTime()->since(),
            ])
            ->filters([
                SelectFilter::make('collection_name')
                    ->label('Collection')
                    ->options(collect(MediaCollection::cases())->mapWithKeys(
                        fn (MediaCollection $collection): array => [$collection->value => $collection->label()]
                    )),
                SelectFilter::make('model_id')
                    ->label('Folder')
                    ->options(fn (): array => MediaFolder::query()->orderBy('name')->pluck('name', 'id')->all()),
            ])
            ->recordActions([
                EditAction::make()
                    ->mutateRecordDataUsing(function (array $data, Media $record): array {
                        $data['alt'] = $record->alt();

                        return $data;
                    })
                    ->using(function (Media $record, array $data): Media {
                        $record->name = $data['name'];
                        $record->setCustomProperty('alt', $data['alt'] ?? '');
                        $record->save();

                        return $record;
                    }),
                DeleteAction::make()
                    ->using(function (Media $record): void {
                        try {
                            app(DeleteMedia::class)->handle($record);
                        } catch (RuntimeException $exception) {
                            Notification::make()->danger()->title($exception->getMessage())->send();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function ($records): void {
                            foreach ($records as $record) {
                                if (! $record instanceof Media || $record->isProtectedSiteAsset()) {
                                    continue;
                                }

                                app(DeleteMedia::class)->handle($record);
                            }
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageMedia::route('/'),
        ];
    }
}
