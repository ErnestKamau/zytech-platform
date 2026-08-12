<?php

namespace App\Filament\Resources\PortalAnnouncements;

use App\Core\Filament\BaseResource;
use App\Filament\Resources\PortalAnnouncements\Pages\ManagePortalAnnouncements;
use App\Models\PortalAnnouncement;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PortalAnnouncementResource extends BaseResource
{
    protected static ?string $model = PortalAnnouncement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected static string|\UnitEnum|null $navigationGroup = 'Clients';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Announcements';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required()->maxLength(255),
            Textarea::make('body')->required()->rows(4)->columnSpanFull(),
            Toggle::make('is_published')->default(false),
            DateTimePicker::make('published_at'),
            TextInput::make('sort_order')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable(),
                IconColumn::make('is_published')->boolean(),
                TextColumn::make('published_at')->dateTime(),
                TextColumn::make('sort_order')->sortable(),
            ])
            ->defaultSort('sort_order')
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
        return ['index' => ManagePortalAnnouncements::route('/')];
    }
}
