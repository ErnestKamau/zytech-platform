<?php

namespace App\Filament\Resources\CompanyStatistics;

use App\Core\Filament\BaseResource;
use App\Domains\Company\Services\CompanyService;
use App\Filament\Resources\CompanyStatistics\Pages\ManageCompanyStatistics;
use App\Models\Company;
use App\Models\CompanyStatistic;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CompanyStatisticResource extends BaseResource
{
    protected static ?string $model = CompanyStatistic::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static string|\UnitEnum|null $navigationGroup = 'Company';

    protected static ?int $navigationSort = 9;

    protected static ?string $navigationLabel = 'Statistics';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('company_id')->default(fn () => Company::query()->value('id')),
                TextInput::make('label')->required(),
                TextInput::make('value')->required(),
                Toggle::make('is_visible')->default(true),
                TextInput::make('sort_order')->numeric()->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')->searchable(),
                TextColumn::make('value'),
                IconColumn::make('is_visible')->boolean(),
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
            'index' => ManageCompanyStatistics::route('/'),
        ];
    }
}
