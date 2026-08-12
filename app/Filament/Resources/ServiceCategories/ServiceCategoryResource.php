<?php

namespace App\Filament\Resources\ServiceCategories;

use App\Core\Filament\BaseResource;
use App\Domains\Service\Services\ServiceService;
use App\Filament\Resources\ServiceCategories\Pages\ManageServiceCategories;
use App\Models\ServiceCategory;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ServiceCategoryResource extends BaseResource
{
    protected static ?string $model = ServiceCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string|\UnitEnum|null $navigationGroup = 'Services';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'category';

    protected static ?string $navigationLabel = 'Categories';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required()->maxLength(255),
                Textarea::make('description')->rows(3)->columnSpanFull(),
                Textarea::make('icon_path')
                    ->rows(2)
                    ->helperText('Optional SVG path for the category icon.')
                    ->columnSpanFull(),
                Toggle::make('is_published')->default(true),
                TextInput::make('sort_order')->numeric()->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('slug'),
                IconColumn::make('is_published')->boolean(),
                TextColumn::make('services_count')->counts('services')->label('Services'),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make()
                    ->after(fn () => app(ServiceService::class)->forget()),
                DeleteAction::make()
                    ->after(fn () => app(ServiceService::class)->forget()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->after(fn () => app(ServiceService::class)->forget()),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageServiceCategories::route('/'),
        ];
    }
}
