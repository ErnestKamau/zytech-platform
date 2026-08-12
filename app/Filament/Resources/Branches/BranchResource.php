<?php

namespace App\Filament\Resources\Branches;

use App\Core\Enums\BranchType;
use App\Core\Filament\BaseResource;
use App\Domains\Company\Services\CompanyService;
use App\Filament\Resources\Branches\Pages\ManageBranches;
use App\Models\Branch;
use App\Models\Company;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BranchResource extends BaseResource
{
    protected static ?string $model = Branch::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static string|\UnitEnum|null $navigationGroup = 'Company';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('company_id')->default(fn () => Company::query()->value('id')),
                TextInput::make('name')->required(),
                Select::make('type')
                    ->options(collect(BranchType::cases())->mapWithKeys(
                        fn (BranchType $type): array => [$type->value => $type->label()]
                    ))
                    ->required()
                    ->default(BranchType::Branch->value),
                TextInput::make('address'),
                TextInput::make('city'),
                TextInput::make('county'),
                TextInput::make('phone'),
                TextInput::make('email')->email(),
                Toggle::make('is_primary'),
                TextInput::make('sort_order')->numeric()->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (BranchType $state): string => $state->label()),
                TextColumn::make('city'),
                IconColumn::make('is_primary')->boolean()->label('Primary'),
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
            'index' => ManageBranches::route('/'),
        ];
    }
}
