<?php

namespace App\Filament\Resources\FeatureFlags;

use App\Core\Enums\FeatureStatus;
use App\Core\Filament\BaseResource;
use App\Domains\Configuration\Actions\DisableFeature;
use App\Domains\Configuration\Actions\EnableFeature;
use App\Filament\Resources\FeatureFlags\Pages\ManageFeatureFlags;
use App\Models\FeatureFlag;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FeatureFlagResource extends BaseResource
{
    protected static ?string $model = FeatureFlag::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFlag;

    protected static string|\UnitEnum|null $navigationGroup = 'Configuration';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Feature flags';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText('Stable machine key such as client_portal'),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(collect(FeatureStatus::cases())->mapWithKeys(
                        fn (FeatureStatus $status): array => [$status->value => $status->label()]
                    ))
                    ->required()
                    ->default(FeatureStatus::Disabled->value),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('key')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (FeatureStatus $state): string => $state === FeatureStatus::Enabled ? 'success' : 'gray')
                    ->formatStateUsing(fn (FeatureStatus $state): string => $state->label()),
                TextColumn::make('description')
                    ->limit(50)
                    ->toggleable(),
            ])
            ->recordActions([
                Action::make('enable')
                    ->visible(fn (FeatureFlag $record): bool => ! $record->isEnabled())
                    ->requiresConfirmation()
                    ->action(fn (FeatureFlag $record) => app(EnableFeature::class)->handle($record->key)),
                Action::make('disable')
                    ->visible(fn (FeatureFlag $record): bool => $record->isEnabled())
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (FeatureFlag $record) => app(DisableFeature::class)->handle($record->key)),
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
            'index' => ManageFeatureFlags::route('/'),
        ];
    }
}
