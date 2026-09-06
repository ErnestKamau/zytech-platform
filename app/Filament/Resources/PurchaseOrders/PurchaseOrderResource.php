<?php

namespace App\Filament\Resources\PurchaseOrders;

use App\Core\Enums\PurchaseOrderStatus;
use App\Core\Filament\BaseResource;
use App\Domains\Commerce\Services\PurchaseOrderService;
use App\Filament\Resources\PurchaseOrders\Pages\ManagePurchaseOrders;
use App\Models\PurchaseOrder;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PurchaseOrderResource extends BaseResource
{
    protected static ?string $model = PurchaseOrder::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCheck;

    protected static string|\UnitEnum|null $navigationGroup = 'Commerce';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Purchase orders';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('reference_number')->disabled(),
            TextInput::make('po_number'),
            Select::make('status')
                ->options(collect(PurchaseOrderStatus::cases())->mapWithKeys(
                    fn (PurchaseOrderStatus $status): array => [$status->value => $status->label()]
                ))
                ->required(),
            Textarea::make('notes')->rows(2)->columnSpanFull(),
            Textarea::make('validation_notes')->rows(2)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference_number')->searchable(),
                TextColumn::make('po_number')->label('PO #'),
                TextColumn::make('client.name')->label('Client'),
                TextColumn::make('quotation.reference_number')->label('Quote'),
                TextColumn::make('status')->badge()->formatStateUsing(
                    fn (PurchaseOrderStatus $state): string => $state->label()
                ),
            ])
            ->defaultSort('updated_at', 'desc')
            ->recordActions([
                Action::make('accept')
                    ->visible(fn (PurchaseOrder $record): bool => in_array($record->status, [
                        PurchaseOrderStatus::Uploaded,
                        PurchaseOrderStatus::UnderReview,
                    ], true))
                    ->requiresConfirmation()
                    ->action(fn (PurchaseOrder $record) => app(PurchaseOrderService::class)->accept($record)),
                Action::make('mismatch')
                    ->visible(fn (PurchaseOrder $record): bool => in_array($record->status, [
                        PurchaseOrderStatus::Uploaded,
                        PurchaseOrderStatus::UnderReview,
                    ], true))
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (PurchaseOrder $record) => app(PurchaseOrderService::class)->markMismatch($record, 'PO does not match accepted quote')),
                Action::make('not_required')
                    ->visible(fn (PurchaseOrder $record): bool => $record->status === PurchaseOrderStatus::Awaiting)
                    ->action(fn (PurchaseOrder $record) => $record->forceFill([
                        'status' => PurchaseOrderStatus::NotRequired,
                        'reviewed_at' => now(),
                        'reviewed_by' => auth()->id(),
                    ])->save()),
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePurchaseOrders::route('/'),
        ];
    }
}
