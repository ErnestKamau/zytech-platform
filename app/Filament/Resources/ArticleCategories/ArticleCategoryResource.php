<?php

namespace App\Filament\Resources\ArticleCategories;

use App\Core\Filament\BaseResource;
use App\Domains\Knowledge\Services\KnowledgeCentreService;
use App\Filament\Resources\ArticleCategories\Pages\ManageArticleCategories;
use App\Models\ArticleCategory;
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

class ArticleCategoryResource extends BaseResource
{
    protected static ?string $model = ArticleCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string|\UnitEnum|null $navigationGroup = 'Knowledge Centre';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'category';

    protected static ?string $navigationLabel = 'Categories';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required()->maxLength(255),
                Textarea::make('description')->rows(3)->columnSpanFull(),
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
                TextColumn::make('articles_count')->counts('articles')->label('Articles'),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make()->after(fn () => app(KnowledgeCentreService::class)->forget()),
                DeleteAction::make()->after(fn () => app(KnowledgeCentreService::class)->forget()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->after(fn () => app(KnowledgeCentreService::class)->forget()),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageArticleCategories::route('/')];
    }
}
