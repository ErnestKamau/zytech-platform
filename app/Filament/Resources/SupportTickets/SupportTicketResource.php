<?php

namespace App\Filament\Resources\SupportTickets;

use App\Core\Enums\PriorityLevel;
use App\Core\Enums\TicketStatus;
use App\Core\Filament\BaseResource;
use App\Filament\Resources\SupportTickets\Pages\ManageSupportTickets;
use App\Models\SupportTicket;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SupportTicketResource extends BaseResource
{
    protected static ?string $model = SupportTicket::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLifebuoy;

    protected static string|\UnitEnum|null $navigationGroup = 'Clients';

    protected static ?int $navigationSort = 11;

    protected static ?string $navigationLabel = 'Support tickets';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('reference_number')->disabled(),
            Select::make('client_id')->relationship('client', 'name')->searchable()->required(),
            TextInput::make('subject')->required(),
            Textarea::make('body')->required()->rows(3)->columnSpanFull(),
            Select::make('status')->options(collect(TicketStatus::cases())->mapWithKeys(
                fn (TicketStatus $status): array => [$status->value => $status->label()]
            ))->required(),
            Select::make('priority')->options(collect(PriorityLevel::cases())->mapWithKeys(
                fn (PriorityLevel $level): array => [$level->value => $level->label()]
            ))->required(),
            Select::make('assigned_to')->relationship('assignee', 'name')->searchable(),
            Repeater::make('replies')->relationship()->schema([
                Textarea::make('body')->required()->rows(2),
                Toggle::make('is_staff')->default(true),
                Select::make('user_id')->relationship('author', 'name')->searchable()->required(),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference_number')->searchable(),
                TextColumn::make('client.name')->searchable(),
                TextColumn::make('subject')->limit(40),
                TextColumn::make('status')->badge()->formatStateUsing(
                    fn (TicketStatus $state): string => $state->label()
                ),
                TextColumn::make('priority')->badge()->formatStateUsing(
                    fn (PriorityLevel $state): string => $state->label()
                ),
                TextColumn::make('created_at')->dateTime(),
            ])
            ->defaultSort('created_at', 'desc')
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
        return ['index' => ManageSupportTickets::route('/')];
    }
}
