<?php

namespace App\Filament\Resources\MediaFolders;

use App\Core\Enums\MediaVisibility;
use App\Core\Filament\BaseResource;
use App\Filament\Resources\MediaFolders\Pages\ManageMediaFolders;
use App\Models\MediaFolder;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MediaFolderResource extends BaseResource
{
    protected static ?string $model = MediaFolder::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolder;

    protected static string|\UnitEnum|null $navigationGroup = 'Media';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Folders';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required(),
                Select::make('parent_id')
                    ->label('Parent')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('visibility')
                    ->options(collect(MediaVisibility::cases())->mapWithKeys(
                        fn (MediaVisibility $visibility): array => [$visibility->value => $visibility->label()]
                    ))
                    ->required()
                    ->default(MediaVisibility::Public->value),
                TextInput::make('sort_order')->numeric()->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('parent.name')->placeholder('—'),
                TextColumn::make('visibility')
                    ->badge()
                    ->formatStateUsing(fn (MediaVisibility $state): string => $state->label()),
                TextColumn::make('media_count')
                    ->counts('media')
                    ->label('Files'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageMediaFolders::route('/'),
        ];
    }
}
