<?php

namespace App\Filament\Resources\Articles;

use App\Core\Enums\ArticleStatus;
use App\Core\Enums\ArticleType;
use App\Core\Enums\ReadingLevel;
use App\Core\Enums\VisibilityStatus;
use App\Core\Filament\BaseResource;
use App\Domains\Knowledge\Actions\ArchiveArticle;
use App\Domains\Knowledge\Actions\FeatureArticle;
use App\Domains\Knowledge\Actions\PublishArticle;
use App\Domains\Knowledge\Services\KnowledgeCentreService;
use App\Filament\Resources\Articles\Pages\ManageArticles;
use App\Models\Article;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ArticleResource extends BaseResource
{
    protected static ?string $model = Article::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static string|\UnitEnum|null $navigationGroup = 'Knowledge Centre';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Articles';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')->required()->maxLength(255),
                Select::make('article_category_id')
                    ->relationship('category', 'name')
                    ->required()
                    ->searchable(),
                Select::make('article_author_id')
                    ->relationship('author', 'name')
                    ->required()
                    ->searchable(),
                Select::make('type')
                    ->options(collect(ArticleType::cases())->mapWithKeys(
                        fn (ArticleType $type): array => [$type->value => $type->label()]
                    ))
                    ->required(),
                Select::make('status')
                    ->options(collect(ArticleStatus::cases())->mapWithKeys(
                        fn (ArticleStatus $status): array => [$status->value => $status->label()]
                    ))
                    ->required()
                    ->default(ArticleStatus::Draft->value),
                Select::make('visibility')
                    ->options(collect(VisibilityStatus::cases())->mapWithKeys(
                        fn (VisibilityStatus $status): array => [$status->value => $status->label()]
                    ))
                    ->required()
                    ->default(VisibilityStatus::Public->value),
                Select::make('reading_level')
                    ->options(collect(ReadingLevel::cases())->mapWithKeys(
                        fn (ReadingLevel $level): array => [$level->value => $level->label()]
                    ))
                    ->required()
                    ->default(ReadingLevel::Beginner->value),
                TextInput::make('reading_time_minutes')->numeric()->minValue(1)->default(1),
                Toggle::make('is_featured'),
                TextInput::make('sort_order')->numeric()->default(0),
                DateTimePicker::make('published_at'),
                Textarea::make('excerpt')->rows(2)->columnSpanFull(),
                Select::make('image_key')
                    ->options(self::mediaImageOptions())
                    ->searchable()
                    ->helperText('Public site image from config/zyntech-media.php.'),
                TextInput::make('meta_title')->maxLength(255)->columnSpanFull(),
                Textarea::make('meta_description')->rows(2)->columnSpanFull(),
                Select::make('og_image_key')->options(self::mediaImageOptions())->searchable(),
                CheckboxList::make('tags')
                    ->relationship('tags', 'name')
                    ->columns(2)
                    ->columnSpanFull(),
                CheckboxList::make('projects')
                    ->relationship('projects', 'title')
                    ->columns(2)
                    ->columnSpanFull(),
                CheckboxList::make('services')
                    ->relationship('services', 'title')
                    ->columns(2)
                    ->columnSpanFull(),
                Repeater::make('sections')
                    ->relationship()
                    ->schema([
                        TextInput::make('heading'),
                        Textarea::make('body')->required()->rows(4),
                        Select::make('image_key')->options(self::mediaImageOptions()),
                    ])
                    ->orderColumn('sort_order')
                    ->collapsible()
                    ->columnSpanFull(),
                Repeater::make('faqs')
                    ->relationship()
                    ->schema([
                        TextInput::make('question')->required(),
                        Textarea::make('answer')->required()->rows(3),
                        Toggle::make('is_published')->default(true),
                    ])
                    ->orderColumn('sort_order')
                    ->collapsible()
                    ->columnSpanFull(),
                Repeater::make('downloads')
                    ->relationship()
                    ->schema([
                        TextInput::make('title')->required(),
                        Textarea::make('description')->rows(2),
                        TextInput::make('file_key')->helperText('Public file key when available.'),
                        TextInput::make('external_url')->url(),
                    ])
                    ->orderColumn('sort_order')
                    ->collapsible()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('category.name')->label('Category'),
                TextColumn::make('author.name')->label('Author'),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (ArticleType $state): string => $state->label()),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (ArticleStatus $state): string => $state->label()),
                IconColumn::make('is_featured')->boolean(),
                TextColumn::make('reading_time_minutes')->suffix(' min')->label('Read time'),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                Action::make('publish')
                    ->visible(fn (Article $record): bool => $record->status !== ArticleStatus::Published)
                    ->requiresConfirmation()
                    ->action(fn (Article $record) => app(PublishArticle::class)->handle($record)),
                Action::make('feature')
                    ->visible(fn (Article $record): bool => ! $record->is_featured && $record->isPublished())
                    ->action(fn (Article $record) => app(FeatureArticle::class)->handle($record, true)),
                Action::make('unfeature')
                    ->visible(fn (Article $record): bool => $record->is_featured)
                    ->action(fn (Article $record) => app(FeatureArticle::class)->handle($record, false)),
                Action::make('archive')
                    ->visible(fn (Article $record): bool => $record->status !== ArticleStatus::Archived)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Article $record) => app(ArchiveArticle::class)->handle($record)),
                EditAction::make()
                    ->after(fn (Article $record) => app(KnowledgeCentreService::class)->persisted($record)),
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
        return ['index' => ManageArticles::route('/')];
    }
}
