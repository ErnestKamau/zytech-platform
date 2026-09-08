<?php

namespace App\Filament\Resources\FollowUps;

use App\Core\Enums\FollowUpStatus;
use App\Core\Filament\BaseResource;
use App\Domains\Operations\Services\FollowUpService;
use App\Filament\Resources\FollowUps\Pages\ManageFollowUps;
use App\Models\FollowUp;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FollowUpResource extends BaseResource
{
    protected static ?string $model = FollowUp::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBellAlert;

    protected static string|\UnitEnum|null $navigationGroup = 'Clients';

    protected static ?int $navigationSort = 20;

    protected static ?string $navigationLabel = 'Follow-ups';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('client_id')->relationship('client', 'name')->searchable()->required(),
            Select::make('quotation_request_id')->relationship('quotationRequest', 'reference_number')->searchable(),
            Select::make('order_id')->relationship('order', 'order_number')->searchable(),
            Select::make('sales_order_id')->relationship('salesOrder', 'reference_number')->searchable(),
            Select::make('assigned_to')->relationship('assignee', 'name')->searchable(),
            DateTimePicker::make('due_at'),
            Textarea::make('notes')->rows(4)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client.name')->searchable(),
                TextColumn::make('assignee.name')->label('Assignee'),
                TextColumn::make('status')->badge()->formatStateUsing(
                    fn (FollowUpStatus $state): string => $state->label()
                ),
                TextColumn::make('due_at')->dateTime()->sortable(),
                TextColumn::make('completed_at')->dateTime()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(
                    collect(FollowUpStatus::cases())->mapWithKeys(
                        fn (FollowUpStatus $status): array => [$status->value => $status->label()]
                    )->all()
                ),
            ])
            ->defaultSort('due_at')
            ->headerActions([
                CreateAction::make()
                    ->using(fn (array $data): FollowUp => app(FollowUpService::class)->create($data)),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('complete')
                    ->visible(fn (FollowUp $record): bool => $record->status === FollowUpStatus::Open)
                    ->action(fn (FollowUp $record) => app(FollowUpService::class)->complete($record)),
                Action::make('cancel')
                    ->color('danger')
                    ->visible(fn (FollowUp $record): bool => $record->status === FollowUpStatus::Open)
                    ->requiresConfirmation()
                    ->action(fn (FollowUp $record) => app(FollowUpService::class)->cancel($record)),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageFollowUps::route('/'),
        ];
    }
}
