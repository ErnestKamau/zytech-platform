<?php

namespace App\Filament\Resources\QuotationRequests;

use App\Core\Enums\BudgetRange;
use App\Core\Enums\PreferredContactMethod;
use App\Core\Enums\ProjectType;
use App\Core\Enums\QuotationStatus;
use App\Core\Filament\BaseResource;
use App\Domains\Quotation\Actions\CreateQuotationFromRequest;
use App\Filament\Resources\QuotationRequests\Pages\ManageQuotationRequests;
use App\Models\QuotationRequest;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuotationRequestResource extends BaseResource
{
    protected static ?string $model = QuotationRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInbox;

    protected static string|\UnitEnum|null $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Requests';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('reference_number')->disabled(),
            TextInput::make('full_name')->required(),
            TextInput::make('email')->email()->required(),
            TextInput::make('phone'),
            Select::make('project_type')->options(collect(ProjectType::cases())->mapWithKeys(
                fn (ProjectType $type): array => [$type->value => $type->label()]
            ))->required(),
            TextInput::make('county'),
            TextInput::make('location'),
            Select::make('budget_range')->options(collect(BudgetRange::cases())->mapWithKeys(
                fn (BudgetRange $range): array => [$range->value => $range->label()]
            )),
            TextInput::make('estimated_timeline'),
            Select::make('preferred_contact_method')->options(collect(PreferredContactMethod::cases())->mapWithKeys(
                fn (PreferredContactMethod $method): array => [$method->value => $method->label()]
            )),
            Select::make('status')->options(collect(QuotationStatus::cases())->mapWithKeys(
                fn (QuotationStatus $status): array => [$status->value => $status->label()]
            ))->required(),
            Textarea::make('description')->rows(4)->columnSpanFull(),
            Textarea::make('internal_notes')->rows(3)->columnSpanFull(),
            CheckboxList::make('services')->relationship('services', 'title')->columns(2)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference_number')->searchable()->sortable(),
                TextColumn::make('full_name')->searchable(),
                TextColumn::make('email'),
                TextColumn::make('project_type')->badge()->formatStateUsing(
                    fn (ProjectType $state): string => $state->label()
                ),
                TextColumn::make('status')->badge()->formatStateUsing(
                    fn (QuotationStatus $state): string => $state->label()
                ),
                TextColumn::make('submitted_at')->dateTime()->sortable(),
            ])
            ->defaultSort('submitted_at', 'desc')
            ->recordActions([
                Action::make('create_quotation')
                    ->visible(fn (QuotationRequest $record): bool => $record->quotation === null)
                    ->requiresConfirmation()
                    ->action(fn (QuotationRequest $record) => app(CreateQuotationFromRequest::class)->handle($record)),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageQuotationRequests::route('/')];
    }
}
