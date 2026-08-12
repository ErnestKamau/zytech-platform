<?php

namespace App\Filament\Resources\MediaTags;

use App\Core\Filament\BaseResource;
use App\Filament\Resources\MediaTags\Pages\ManageMediaTags;
use App\Models\MediaTag;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MediaTagResource extends BaseResource
{
    protected static ?string $model = MediaTag::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static string|\UnitEnum|null $navigationGroup = 'Media';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Tags';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('media_count')->counts('media')->label('Files'),
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
            'index' => ManageMediaTags::route('/'),
        ];
    }
}
