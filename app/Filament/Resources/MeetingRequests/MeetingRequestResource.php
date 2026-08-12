<?php

namespace App\Filament\Resources\MeetingRequests;

use App\Core\Enums\MeetingStatus;
use App\Core\Enums\MeetingType;
use App\Core\Filament\BaseResource;
use App\Filament\Resources\MeetingRequests\Pages\ManageMeetingRequests;
use App\Models\MeetingRequest;
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

class MeetingRequestResource extends BaseResource
{
    protected static ?string $model = MeetingRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|\UnitEnum|null $navigationGroup = 'Clients';

    protected static ?int $navigationSort = 12;

    protected static ?string $navigationLabel = 'Meetings';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('client_id')->relationship('client', 'name')->searchable()->required(),
            Select::make('meeting_type')->options(collect(MeetingType::cases())->mapWithKeys(
                fn (MeetingType $type): array => [$type->value => $type->label()]
            ))->required(),
            Select::make('status')->options(collect(MeetingStatus::cases())->mapWithKeys(
                fn (MeetingStatus $status): array => [$status->value => $status->label()]
            ))->required(),
            DateTimePicker::make('scheduled_at'),
            TextInput::make('location'),
            Select::make('assigned_to')->relationship('assignee', 'name')->searchable(),
            Textarea::make('notes')->rows(3)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client.name')->searchable(),
                TextColumn::make('meeting_type')->badge()->formatStateUsing(
                    fn (MeetingType $state): string => $state->label()
                ),
                TextColumn::make('status')->badge()->formatStateUsing(
                    fn (MeetingStatus $state): string => $state->label()
                ),
                TextColumn::make('scheduled_at')->dateTime(),
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
        return ['index' => ManageMeetingRequests::route('/')];
    }
}
