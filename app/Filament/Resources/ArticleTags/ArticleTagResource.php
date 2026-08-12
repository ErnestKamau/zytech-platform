<?php

namespace App\Filament\Resources\ArticleTags;

use App\Core\Filament\BaseResource;
use App\Domains\Knowledge\Services\KnowledgeCentreService;
use App\Filament\Resources\ArticleTags\Pages\ManageArticleTags;
use App\Models\ArticleTag;
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

class ArticleTagResource extends BaseResource
{
    protected static ?string $model = ArticleTag::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static string|\UnitEnum|null $navigationGroup = 'Knowledge Centre';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'tag';

    protected static ?string $navigationLabel = 'Tags';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required()->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('slug'),
            ])
            ->defaultSort('name')
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
        return ['index' => ManageArticleTags::route('/')];
    }
}
