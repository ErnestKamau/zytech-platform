<?php

namespace App\Filament\Resources\NavigationMenus;

use App\Core\Enums\NavigationLocation;
use App\Core\Filament\BaseResource;
use App\Domains\Configuration\Actions\PublishNavigation;
use App\Filament\Resources\NavigationMenus\Pages\ManageNavigationMenus;
use App\Models\NavigationMenu;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NavigationMenuResource extends BaseResource
{
    protected static ?string $model = NavigationMenu::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBars3;

    protected static string|\UnitEnum|null $navigationGroup = 'Configuration';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Navigation';

    protected static ?string $modelLabel = 'navigation menu';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Select::make('location')
                    ->options(collect(NavigationLocation::cases())->mapWithKeys(
                        fn (NavigationLocation $location): array => [$location->value => $location->label()]
                    ))
                    ->required(),
                Toggle::make('is_published')
                    ->helperText('Publishing a menu unpublishes others in the same location.'),
                Repeater::make('items')
                    ->relationship()
                    ->schema([
                        TextInput::make('label')->required(),
                        TextInput::make('route_name')
                            ->helperText('Named Laravel route, e.g. home'),
                        TextInput::make('url')
                            ->helperText('Used when no route name is set'),
                        Select::make('target')
                            ->options([
                                '_self' => 'Same tab',
                                '_blank' => 'New tab',
                            ])
                            ->default('_self')
                            ->required(),
                        Toggle::make('is_visible')->default(true),
                        TextInput::make('sort_order')->numeric()->default(0),
                    ])
                    ->orderColumn('sort_order')
                    ->collapsible()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('location')
                    ->badge()
                    ->formatStateUsing(fn (NavigationLocation $state): string => $state->label()),
                IconColumn::make('is_published')
                    ->boolean()
                    ->label('Published'),
                TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Items'),
            ])
            ->recordActions([
                Action::make('publish')
                    ->visible(fn (NavigationMenu $record): bool => ! $record->is_published)
                    ->requiresConfirmation()
                    ->action(fn (NavigationMenu $record) => app(PublishNavigation::class)->handle($record)),
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
            'index' => ManageNavigationMenus::route('/'),
        ];
    }
}
