<?php

namespace App\Filament\Resources\Settings;

use App\Core\Enums\SettingType;
use App\Core\Filament\BaseResource;
use App\Domains\Configuration\Actions\ClearConfigurationCache;
use App\Domains\Configuration\Events\SettingsUpdated;
use App\Filament\Resources\Settings\Pages\ManageSettings;
use App\Models\Setting;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SettingResource extends BaseResource
{
    protected static ?string $model = Setting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|\UnitEnum|null $navigationGroup = 'Configuration';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('setting_group_id')
                    ->relationship('group', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('key')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText('Dotted key such as contact.email'),
                TextInput::make('label')
                    ->required()
                    ->maxLength(255),
                Select::make('type')
                    ->options(collect(SettingType::cases())->mapWithKeys(
                        fn (SettingType $type): array => [$type->value => $type->label()]
                    ))
                    ->required(),
                Textarea::make('value')
                    ->rows(3)
                    ->columnSpanFull(),
                Toggle::make('is_public')
                    ->default(true),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('group.name')
                    ->label('Group')
                    ->sortable(),
                TextColumn::make('key')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('label')
                    ->searchable(),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (?SettingType $state): string => $state?->label() ?? '—'),
                TextColumn::make('value')
                    ->limit(40),
                IconColumn::make('is_public')
                    ->boolean()
                    ->label('Public'),
            ])
            ->filters([
                SelectFilter::make('setting_group_id')
                    ->relationship('group', 'name')
                    ->label('Group'),
            ])
            ->recordActions([
                EditAction::make()
                    ->after(function (Setting $record): void {
                        app(ClearConfigurationCache::class)->handle();
                        event(new SettingsUpdated([$record->key]));
                    }),
                DeleteAction::make()
                    ->after(function (Setting $record): void {
                        app(ClearConfigurationCache::class)->handle();
                        event(new SettingsUpdated([$record->key]));
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('key');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSettings::route('/'),
        ];
    }
}
