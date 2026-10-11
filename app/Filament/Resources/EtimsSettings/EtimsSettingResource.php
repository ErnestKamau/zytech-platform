<?php

namespace App\Filament\Resources\EtimsSettings;

use App\Core\Filament\BaseResource;
use App\Domains\Commerce\Services\Etims\EtimsCatalogSyncService;
use App\Filament\Resources\EtimsSettings\Pages\ManageEtimsSettings;
use App\Models\EtimsSetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EtimsSettingResource extends BaseResource
{
    protected static ?string $model = EtimsSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static string|\UnitEnum|null $navigationGroup = 'Commerce';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'eTIMS settings';

    public static function canCreate(): bool
    {
        return EtimsSetting::query()->doesntExist();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Toggle::make('enabled')
                ->label('Enabled')
                ->helperText('When off, issued invoices are never submitted to KRA.'),
            Select::make('environment')
                ->options(['production' => 'Production', 'sandbox' => 'Sandbox'])
                ->required()
                ->default('production'),
            TextInput::make('device_serial')
                ->label('Device serial')
                ->helperText('KRA-registered VSCU device serial.'),

            Section::make('Default service item')
                ->description('Used for invoice lines that are not linked to a KRA-synced Product.')
                ->columns(2)
                ->schema([
                    TextInput::make('default_item_classification_code')
                        ->label('Classification code (itemClsCd)'),
                    TextInput::make('default_item_code')
                        ->label('Registered item code')
                        ->disabled()
                        ->dehydrated(false)
                        ->helperText('Populated after syncing below.'),
                ]),

            Section::make('Advanced')
                ->collapsed()
                ->schema([
                    TextInput::make('base_url_override')
                        ->label('Base URL override')
                        ->helperText('Leave blank to use ETIMS_BASE_URL from config.'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('enabled')->boolean(),
                TextColumn::make('environment'),
                TextColumn::make('device_serial'),
                TextColumn::make('default_item_code')->label('Default item code')->placeholder('Not synced'),
            ])
            ->recordActions([
                Action::make('syncDefaultItem')
                    ->label('Sync default item')
                    ->icon(Heroicon::OutlinedArrowPath)
                    ->requiresConfirmation()
                    ->modalDescription('Registers the fallback "General Service" item with eTIMS for use on invoice lines that have no linked Product.')
                    ->action(function (EtimsSetting $record): void {
                        try {
                            app(EtimsCatalogSyncService::class)->syncDefaultServiceItem($record);
                            Notification::make()->success()->title('Default service item synced')->send();
                        } catch (\Throwable $e) {
                            Notification::make()->danger()->title('Sync failed')->body($e->getMessage())->send();
                        }
                    }),
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageEtimsSettings::route('/'),
        ];
    }
}
