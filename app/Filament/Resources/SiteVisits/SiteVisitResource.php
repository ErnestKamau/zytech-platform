<?php

namespace App\Filament\Resources\SiteVisits;

use App\Core\Enums\SiteVisitStatus;
use App\Core\Filament\BaseResource;
use App\Filament\Resources\SiteVisits\Pages\ManageSiteVisits;
use App\Models\SiteVisit;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SiteVisitResource extends BaseResource
{
    protected static ?string $model = SiteVisit::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static string|\UnitEnum|null $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Site visits';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('quotation_request_id')->relationship('quotationRequest', 'reference_number')->searchable(),
            Select::make('sales_lead_id')->relationship('salesLead', 'full_name')->searchable(),
            Select::make('visit_type')->options([
                'site' => 'Site visit',
                'virtual' => 'Virtual meeting',
                'phone' => 'Phone consultation',
            ])->required(),
            Select::make('status')->options(collect(SiteVisitStatus::cases())->mapWithKeys(
                fn (SiteVisitStatus $status): array => [$status->value => $status->label()]
            ))->required(),
            DateTimePicker::make('scheduled_at')->required(),
            TextInput::make('location'),
            TextInput::make('engineer_name'),
            Textarea::make('notes')->rows(3)->columnSpanFull(),
            Textarea::make('recommendations')->rows(3)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('quotationRequest.reference_number')->label('Request'),
                TextColumn::make('visit_type')->badge(),
                TextColumn::make('status')->badge()->formatStateUsing(
                    fn (SiteVisitStatus $state): string => $state->label()
                ),
                TextColumn::make('scheduled_at')->dateTime()->sortable(),
                TextColumn::make('location'),
            ])
            ->defaultSort('scheduled_at', 'desc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageSiteVisits::route('/')];
    }
}
