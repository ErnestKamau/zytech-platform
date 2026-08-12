<?php

namespace App\Filament\Resources\Awards;

use App\Core\Enums\AwardCategory;
use App\Core\Filament\BaseResource;
use App\Domains\Company\Services\CompanyService;
use App\Filament\Resources\Awards\Pages\ManageAwards;
use App\Models\Award;
use App\Models\Company;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AwardResource extends BaseResource
{
    protected static ?string $model = Award::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;

    protected static string|\UnitEnum|null $navigationGroup = 'Company';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('company_id')->default(fn () => Company::query()->value('id')),
                TextInput::make('title')->required(),
                Select::make('category')
                    ->options(collect(AwardCategory::cases())->mapWithKeys(
                        fn (AwardCategory $category): array => [$category->value => $category->label()]
                    ))
                    ->required(),
                TextInput::make('year')->numeric(),
                TextInput::make('issuer'),
                Textarea::make('description')->rows(3)->columnSpanFull(),
                TextInput::make('sort_order')->numeric()->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable(),
                TextColumn::make('category')
                    ->badge()
                    ->formatStateUsing(fn (AwardCategory $state): string => $state->label()),
                TextColumn::make('year'),
            ])
            ->recordActions([
                EditAction::make()
                    ->after(fn () => app(CompanyService::class)->forget()),
                DeleteAction::make()
                    ->after(fn () => app(CompanyService::class)->forget()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAwards::route('/'),
        ];
    }
}
