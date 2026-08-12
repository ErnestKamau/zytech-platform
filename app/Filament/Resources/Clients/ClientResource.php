<?php

namespace App\Filament\Resources\Clients;

use App\Core\Enums\ClientStatus;
use App\Core\Enums\ClientType;
use App\Core\Enums\PreferredContactMethod;
use App\Core\Filament\BaseResource;
use App\Domains\Client\Actions\ArchiveClient;
use App\Domains\Client\Events\ClientUpdated;
use App\Filament\Resources\Clients\Pages\ManageClients;
use App\Models\Client;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClientResource extends BaseResource
{
    protected static ?string $model = Client::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|\UnitEnum|null $navigationGroup = 'Clients';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('type')->options(collect(ClientType::cases())->mapWithKeys(
                fn (ClientType $type): array => [$type->value => $type->label()]
            ))->required(),
            Select::make('status')->options(collect(ClientStatus::cases())->mapWithKeys(
                fn (ClientStatus $status): array => [$status->value => $status->label()]
            ))->required(),
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('legal_name')->maxLength(255),
            TextInput::make('email')->email()->required(),
            TextInput::make('phone'),
            TextInput::make('industry'),
            TextInput::make('website')->url(),
            Select::make('preferred_contact_method')->options(collect(PreferredContactMethod::cases())->mapWithKeys(
                fn (PreferredContactMethod $method): array => [$method->value => $method->label()]
            )),
            Select::make('assigned_sales_id')->relationship('assignedSales', 'name')->searchable(),
            Select::make('assigned_pm_id')->relationship('assignedProjectManager', 'name')->searchable(),
            Select::make('user_id')->relationship('user', 'name')->searchable()->label('Portal user'),
            Textarea::make('summary')->rows(3)->columnSpanFull(),
            CheckboxList::make('tags')->relationship('tags', 'name')->columns(2)->columnSpanFull(),
            CheckboxList::make('groups')->relationship('groups', 'name')->columns(2)->columnSpanFull(),
            CheckboxList::make('projects')->relationship('projects', 'title')->columns(2)->columnSpanFull(),
            Repeater::make('contacts')->relationship()->schema([
                TextInput::make('name')->required(),
                TextInput::make('role'),
                TextInput::make('email')->email(),
                TextInput::make('phone'),
                Toggle::make('is_primary')->default(false),
            ])->orderColumn('sort_order')->collapsible()->columnSpanFull(),
            Repeater::make('addresses')->relationship()->schema([
                TextInput::make('label'),
                TextInput::make('line1')->required(),
                TextInput::make('line2'),
                TextInput::make('city'),
                TextInput::make('county'),
                TextInput::make('country')->default('Kenya'),
                Toggle::make('is_primary')->default(false),
            ])->orderColumn('sort_order')->collapsible()->columnSpanFull(),
            Repeater::make('notes')->relationship()->schema([
                Textarea::make('body')->required()->rows(3),
                Toggle::make('is_internal')->default(true),
            ])->collapsible()->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('phone'),
                TextColumn::make('type')->badge()->formatStateUsing(
                    fn (ClientType $state): string => $state->label()
                ),
                TextColumn::make('status')->badge()->formatStateUsing(
                    fn (ClientStatus $state): string => $state->label()
                ),
                TextColumn::make('assignedSales.name')->label('Sales'),
                TextColumn::make('quotationRequests_count')->counts('quotationRequests')->label('Requests'),
            ])
            ->defaultSort('name')
            ->recordActions([
                Action::make('archive')
                    ->visible(fn (Client $record): bool => $record->status !== ClientStatus::Archived)
                    ->requiresConfirmation()
                    ->action(fn (Client $record) => app(ArchiveClient::class)->handle($record)),
                EditAction::make()->after(fn (Client $record) => event(new ClientUpdated($record->fresh()))),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageClients::route('/')];
    }
}
