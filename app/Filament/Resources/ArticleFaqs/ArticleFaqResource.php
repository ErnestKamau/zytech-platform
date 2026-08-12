<?php

namespace App\Filament\Resources\ArticleFaqs;

use App\Core\Filament\BaseResource;
use App\Domains\Knowledge\Services\KnowledgeCentreService;
use App\Filament\Resources\ArticleFaqs\Pages\ManageArticleFaqs;
use App\Models\ArticleFaq;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ArticleFaqResource extends BaseResource
{
    protected static ?string $model = ArticleFaq::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected static string|\UnitEnum|null $navigationGroup = 'Knowledge Centre';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'article FAQ';

    protected static ?string $navigationLabel = 'FAQs';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('article_id')
                    ->relationship('article', 'title')
                    ->required()
                    ->searchable(),
                TextInput::make('question')->required()->columnSpanFull(),
                Textarea::make('answer')->required()->rows(4)->columnSpanFull(),
                Toggle::make('is_published')->default(true),
                TextInput::make('sort_order')->numeric()->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('article.title')->label('Article')->searchable(),
                TextColumn::make('question')->searchable()->limit(60),
                IconColumn::make('is_published')->boolean(),
            ])
            ->filters([
                SelectFilter::make('article_id')
                    ->relationship('article', 'title')
                    ->label('Article'),
            ])
            ->recordActions([
                EditAction::make()
                    ->after(fn (ArticleFaq $record) => app(KnowledgeCentreService::class)->forget($record->article?->slug)),
                DeleteAction::make()
                    ->after(fn () => app(KnowledgeCentreService::class)->forget()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->after(fn () => app(KnowledgeCentreService::class)->forget()),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageArticleFaqs::route('/')];
    }
}
