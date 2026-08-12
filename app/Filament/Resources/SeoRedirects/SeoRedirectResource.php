<?php

namespace App\Filament\Resources\SeoRedirects;

use App\Core\Filament\BaseResource;
use App\Filament\Resources\SeoRedirects\Pages\ManageSeoRedirects;
use App\Models\SeoRedirect;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SeoRedirectResource extends BaseResource
{
    protected static ?string $model = SeoRedirect::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowPath;

    protected static string|\UnitEnum|null $navigationGroup = 'Configuration';

    protected static ?int $navigationSort = 20;

    protected static ?string $navigationLabel = 'SEO redirects';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('from_path')->required()->placeholder('/old-path'),
            TextInput::make('to_path')->required()->placeholder('/new-path'),
            TextInput::make('status_code')->numeric()->default(301),
            Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('from_path')->searchable(),
                TextColumn::make('to_path')->searchable(),
                TextColumn::make('status_code'),
                IconColumn::make('is_active')->boolean(),
            ])
            ->defaultSort('from_path')
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
        return ['index' => ManageSeoRedirects::route('/')];
    }
}
