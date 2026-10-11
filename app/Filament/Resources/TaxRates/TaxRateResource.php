<?php

namespace App\Filament\Resources\TaxRates;

use App\Core\Filament\BaseResource;
use App\Filament\Resources\TaxRates\Pages\ManageTaxRates;
use App\Models\TaxRate;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TaxRateResource extends BaseResource
{
    protected static ?string $model = TaxRate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptPercent;

    protected static string|\UnitEnum|null $navigationGroup = 'Commerce';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Tax rates';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')->required()->maxLength(50),
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('rate')->label('Rate')->numeric()->minValue(0)->maxValue(100)->suffix('%')->required(),
                Select::make('kra_tax_type_code')
                    ->label('KRA tax band')
                    ->options([
                        'A' => 'A — Exempt',
                        'B' => 'B — Standard (16%)',
                        'C' => 'C — Zero-rated',
                        'D' => 'D — Non-VAT',
                    ])
                    ->helperText('Used when submitting invoices to eTIMS.'),
                Toggle::make('is_default')
                    ->label('Default')
                    ->helperText('Used when a quotation has no rate selected.'),
                Toggle::make('active')->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->searchable()->sortable(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('rate')->suffix('%'),
                TextColumn::make('kra_tax_type_code')->label('KRA band'),
                IconColumn::make('is_default')->boolean(),
                IconColumn::make('active')->boolean(),
            ])
            ->defaultSort('code')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
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
            'index' => ManageTaxRates::route('/'),
        ];
    }
}
