<?php

namespace App\Filament\Resources\ProjectCategories;

use App\Core\Filament\BaseResource;
use App\Domains\Project\Services\ProjectService;
use App\Filament\Resources\ProjectCategories\Pages\ManageProjectCategories;
use App\Models\ProjectCategory;
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

class ProjectCategoryResource extends BaseResource
{
    protected static ?string $model = ProjectCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string|\UnitEnum|null $navigationGroup = 'Projects';

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
                TextColumn::make('projects_count')->counts('projects')->label('Projects'),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make()->after(fn () => app(ProjectService::class)->forget()),
                DeleteAction::make()->after(fn () => app(ProjectService::class)->forget()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->after(fn () => app(ProjectService::class)->forget()),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageProjectCategories::route('/')];
    }
}
