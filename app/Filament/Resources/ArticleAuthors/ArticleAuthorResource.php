<?php

namespace App\Filament\Resources\ArticleAuthors;

use App\Core\Filament\BaseResource;
use App\Domains\Knowledge\Services\KnowledgeCentreService;
use App\Filament\Resources\ArticleAuthors\Pages\ManageArticleAuthors;
use App\Models\ArticleAuthor;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ArticleAuthorResource extends BaseResource
{
    protected static ?string $model = ArticleAuthor::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected static string|\UnitEnum|null $navigationGroup = 'Knowledge Centre';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'author';

    protected static ?string $navigationLabel = 'Authors';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('role')->maxLength(255),
                Textarea::make('bio')->rows(4)->columnSpanFull(),
                Select::make('photo_key')->options(self::mediaImageOptions())->searchable(),
                Toggle::make('is_visible')->default(true),
                TextInput::make('sort_order')->numeric()->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('role'),
                IconColumn::make('is_visible')->boolean(),
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
        return ['index' => ManageArticleAuthors::route('/')];
    }
}
