<?php

namespace App\Filament\Resources\ClientDocuments;

use App\Core\Enums\DocumentVisibility;
use App\Core\Filament\BaseResource;
use App\Filament\Resources\ClientDocuments\Pages\ManageClientDocuments;
use App\Models\ClientDocument;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClientDocumentResource extends BaseResource
{
    protected static ?string $model = ClientDocument::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocument;

    protected static string|\UnitEnum|null $navigationGroup = 'Clients';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Documents';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('client_id')->relationship('client', 'name')->searchable()->required(),
            TextInput::make('title')->required()->maxLength(255),
            TextInput::make('kind')->default('general'),
            FileUpload::make('stored_path')
                ->label('File')
                ->disk('local')
                ->directory('client-documents')
                ->visibility('private')
                ->maxSize(20480),
            TextInput::make('mime_type')->disabled()->dehydrated(),
            TextInput::make('size_bytes')->numeric()->default(0)->disabled()->dehydrated(),
            Select::make('visibility')->options(collect(DocumentVisibility::cases())->mapWithKeys(
                fn (DocumentVisibility $visibility): array => [$visibility->value => $visibility->label()]
            ))->required()->default(DocumentVisibility::Client->value),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client.name')->searchable()->sortable(),
                TextColumn::make('title')->searchable(),
                TextColumn::make('kind'),
                TextColumn::make('visibility')->badge()->formatStateUsing(
                    fn (DocumentVisibility $state): string => $state->label()
                ),
                TextColumn::make('created_at')->dateTime(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageClientDocuments::route('/')];
    }
}
