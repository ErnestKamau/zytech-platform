<?php

namespace App\Filament\Resources\SalesLeads;

use App\Core\Enums\LeadStatus;
use App\Core\Enums\PriorityLevel;
use App\Core\Filament\BaseResource;
use App\Domains\Quotation\Services\LeadService;
use App\Filament\Resources\SalesLeads\Pages\ManageSalesLeads;
use App\Models\SalesLead;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SalesLeadResource extends BaseResource
{
    protected static ?string $model = SalesLead::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserPlus;

    protected static string|\UnitEnum|null $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Leads';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('client_id')
                ->relationship('client', 'name')
                ->disabled()
                ->dehydrated(false)
                ->helperText('Created automatically from the RFQ intake.'),
            Select::make('lead_source_id')
                ->label('Source')
                ->relationship('source', 'name')
                ->searchable()
                ->preload(),
            TextInput::make('full_name')->required()->maxLength(255),
            TextInput::make('email')->email()->maxLength(255),
            TextInput::make('phone')->maxLength(50),
            TextInput::make('company_name')->maxLength(255),
            Select::make('status')
                ->options(collect(LeadStatus::cases())->mapWithKeys(
                    fn (LeadStatus $status): array => [$status->value => $status->label()]
                ))
                ->required(),
            Select::make('priority')
                ->options(collect(PriorityLevel::cases())->mapWithKeys(
                    fn (PriorityLevel $priority): array => [$priority->value => $priority->label()]
                ))
                ->required(),
            Select::make('assigned_to')
                ->label('Assigned to')
                ->relationship('assignee', 'name')
                ->searchable()
                ->preload(),
            Textarea::make('notes')->rows(3)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')->searchable()->sortable(),
                TextColumn::make('client.name')->label('Client'),
                TextColumn::make('email')->searchable(),
                TextColumn::make('phone'),
                TextColumn::make('source.name')->label('Source'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (LeadStatus $state): string => $state->color())
                    ->formatStateUsing(fn (LeadStatus $state): string => $state->label()),
                TextColumn::make('priority')
                    ->badge()
                    ->formatStateUsing(fn (PriorityLevel $state): string => $state->label()),
                TextColumn::make('assignee.name')->label('Assigned to'),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')->options(
                    collect(LeadStatus::cases())->mapWithKeys(
                        fn (LeadStatus $status): array => [$status->value => $status->label()]
                    )->all()
                ),
                Filter::make('mine')
                    ->label('My leads')
                    ->query(fn (Builder $query): Builder => $query->where('assigned_to', auth()->id())),
            ])
            ->recordActions([
                Action::make('contact')
                    ->label('Mark contacted')
                    ->icon(Heroicon::OutlinedPhone)
                    ->visible(fn (SalesLead $record): bool => $record->status === LeadStatus::New)
                    ->action(fn (SalesLead $record) => self::transition($record, 'contact')),
                Action::make('qualify')
                    ->label('Qualify')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (SalesLead $record): bool => in_array($record->status, [LeadStatus::New, LeadStatus::Contacted], true))
                    ->action(fn (SalesLead $record) => self::transition($record, 'qualify')),
                Action::make('convert')
                    ->label('Convert')
                    ->icon(Heroicon::OutlinedTrophy)
                    ->color('success')
                    ->visible(fn (SalesLead $record): bool => $record->status === LeadStatus::Qualified)
                    ->action(fn (SalesLead $record) => self::transition($record, 'convert')),
                Action::make('disqualify')
                    ->label('Disqualify')
                    ->icon(Heroicon::OutlinedXMark)
                    ->color('danger')
                    ->visible(fn (SalesLead $record): bool => ! in_array($record->status, [LeadStatus::Unqualified, LeadStatus::Converted, LeadStatus::Archived], true))
                    ->requiresConfirmation()
                    ->action(fn (SalesLead $record) => self::transition($record, 'disqualify')),
                Action::make('archive')
                    ->label('Archive')
                    ->icon(Heroicon::OutlinedArchiveBox)
                    ->color('gray')
                    ->visible(fn (SalesLead $record): bool => $record->status !== LeadStatus::Archived)
                    ->requiresConfirmation()
                    ->action(fn (SalesLead $record) => self::transition($record, 'archive')),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    private static function transition(SalesLead $record, string $method): void
    {
        try {
            app(LeadService::class)->{$method}($record);
            Notification::make()->success()->title('Lead updated')->send();
        } catch (\Throwable $e) {
            Notification::make()->danger()->title('Could not update lead')->body($e->getMessage())->send();
        }
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSalesLeads::route('/'),
        ];
    }
}
