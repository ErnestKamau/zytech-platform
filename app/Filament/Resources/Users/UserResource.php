<?php

namespace App\Filament\Resources\Users;

use App\Core\Enums\UserType;
use App\Core\Filament\BaseResource;
use App\Domains\Authentication\Actions\LockAccount;
use App\Domains\Authentication\Actions\UnlockAccount;
use App\Filament\Resources\Users\Pages\ManageUsers;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserResource extends BaseResource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|\UnitEnum|null $navigationGroup = 'Identity';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                Select::make('type')
                    ->options(collect(UserType::cases())->mapWithKeys(
                        fn (UserType $type): array => [$type->value => $type->label()]
                    ))
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->maxLength(40),
                TextInput::make('password')
                    ->password()
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create'),
                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
                DateTimePicker::make('email_verified_at')
                    ->label('Email verified at'),
                Toggle::make('mfa_enabled')
                    ->label('MFA preference enabled'),
                DateTimePicker::make('locked_at')
                    ->label('Locked at'),
                TextInput::make('lock_reason')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (?UserType $state): string => $state?->label() ?? '—'),
                TextColumn::make('roles.name')
                    ->badge()
                    ->label('Roles'),
                IconColumn::make('email_verified_at')
                    ->label('Verified')
                    ->boolean()
                    ->getStateUsing(fn (User $record): bool => $record->hasVerifiedEmail()),
                IconColumn::make('locked_at')
                    ->label('Locked')
                    ->boolean()
                    ->getStateUsing(fn (User $record): bool => $record->isLocked()),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
                Action::make('lock')
                    ->visible(fn (User $record): bool => ! $record->isLocked())
                    ->requiresConfirmation()
                    ->action(fn (User $record) => app(LockAccount::class)->handle($record)),
                Action::make('unlock')
                    ->visible(fn (User $record): bool => $record->isLocked())
                    ->requiresConfirmation()
                    ->action(fn (User $record) => app(UnlockAccount::class)->handle($record)),
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
            'index' => ManageUsers::route('/'),
        ];
    }
}
