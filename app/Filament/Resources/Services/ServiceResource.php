<?php

namespace App\Filament\Resources\Services;

use App\Core\Enums\PricingModel;
use App\Core\Enums\ServiceStatus;
use App\Core\Enums\ServiceType;
use App\Core\Enums\VisibilityStatus;
use App\Core\Filament\BaseResource;
use App\Domains\Service\Actions\ArchiveService;
use App\Domains\Service\Actions\FeatureService;
use App\Domains\Service\Actions\PublishService;
use App\Domains\Service\Services\ServiceService;
use App\Filament\Resources\Services\Pages\ManageServices;
use App\Models\Service;
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
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ServiceResource extends BaseResource
{
    protected static ?string $model = Service::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static string|\UnitEnum|null $navigationGroup = 'Services';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')->required()->maxLength(255),
                Select::make('service_category_id')
                    ->relationship('category', 'name')
                    ->required()
                    ->searchable(),
                Select::make('type')
                    ->options(collect(ServiceType::cases())->mapWithKeys(
                        fn (ServiceType $type): array => [$type->value => $type->label()]
                    ))
                    ->required(),
                Select::make('status')
                    ->options(collect(ServiceStatus::cases())->mapWithKeys(
                        fn (ServiceStatus $status): array => [$status->value => $status->label()]
                    ))
                    ->required()
                    ->default(ServiceStatus::Draft->value),
                Select::make('visibility')
                    ->options(collect(VisibilityStatus::cases())->mapWithKeys(
                        fn (VisibilityStatus $status): array => [$status->value => $status->label()]
                    ))
                    ->required()
                    ->default(VisibilityStatus::Public->value),
                Toggle::make('is_featured'),
                TextInput::make('sort_order')->numeric()->default(0),
                Textarea::make('excerpt')->rows(2)->columnSpanFull(),
                Textarea::make('body')->rows(5)->columnSpanFull(),
                Textarea::make('icon_path')
                    ->rows(2)
                    ->helperText('SVG path used when no site image is selected.')
                    ->columnSpanFull(),
                Select::make('image_key')
                    ->options(self::mediaImageOptions())
                    ->searchable()
                    ->helperText('Public site image from config/zyntech-media.php. Original files are not moved.'),
                CheckboxList::make('gallery_keys')
                    ->options(self::mediaImageOptions())
                    ->columns(2)
                    ->columnSpanFull(),
                Select::make('pricing_model')
                    ->options(collect(PricingModel::cases())->mapWithKeys(
                        fn (PricingModel $model): array => [$model->value => $model->label()]
                    ))
                    ->required()
                    ->default(PricingModel::QuoteOnRequest->value),
                TextInput::make('price_amount')->numeric()->prefix('KES'),
                TextInput::make('price_currency')->maxLength(3)->default('KES'),
                TextInput::make('price_unit')->helperText('e.g. per sqm'),
                Textarea::make('pricing_notes')->rows(2)->columnSpanFull(),
                TextInput::make('meta_title')->maxLength(255)->columnSpanFull(),
                Textarea::make('meta_description')->rows(2)->columnSpanFull(),
                Select::make('og_image_key')->options(self::mediaImageOptions())->searchable(),
                Repeater::make('features')
                    ->relationship()
                    ->schema([
                        TextInput::make('title')->required(),
                        Textarea::make('description')->rows(2),
                    ])
                    ->orderColumn('sort_order')
                    ->collapsible()
                    ->columnSpanFull(),
                Repeater::make('processes')
                    ->relationship()
                    ->schema([
                        TextInput::make('title')->required(),
                        Textarea::make('description')->rows(2),
                    ])
                    ->orderColumn('sort_order')
                    ->collapsible()
                    ->columnSpanFull(),
                Repeater::make('statistics')
                    ->relationship()
                    ->schema([
                        TextInput::make('label')->required(),
                        TextInput::make('value')->required(),
                        Toggle::make('is_visible')->default(true),
                    ])
                    ->orderColumn('sort_order')
                    ->collapsible()
                    ->columnSpanFull(),
                Repeater::make('relatedProjects')
                    ->relationship()
                    ->schema([
                        TextInput::make('title')->required(),
                        TextInput::make('slug')->helperText('Optional until the Projects domain lands.'),
                        Textarea::make('summary')->rows(2),
                        Select::make('image_key')->options(self::mediaImageOptions()),
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
                TextColumn::make('title')->searchable()->sortable(),
                TextColumn::make('category.name')->label('Category'),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (ServiceType $state): string => $state->label()),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (ServiceStatus $state): string => $state->label()),
                IconColumn::make('is_featured')->boolean(),
                TextColumn::make('pricing_model')
                    ->formatStateUsing(fn (PricingModel $state): string => $state->label()),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                Action::make('publish')
                    ->visible(fn (Service $record): bool => $record->status !== ServiceStatus::Published)
                    ->requiresConfirmation()
                    ->action(fn (Service $record) => app(PublishService::class)->handle($record)),
                Action::make('feature')
                    ->visible(fn (Service $record): bool => ! $record->is_featured && $record->isPublished())
                    ->action(fn (Service $record) => app(FeatureService::class)->handle($record, true)),
                Action::make('unfeature')
                    ->visible(fn (Service $record): bool => $record->is_featured)
                    ->action(fn (Service $record) => app(FeatureService::class)->handle($record, false)),
                Action::make('archive')
                    ->visible(fn (Service $record): bool => $record->status !== ServiceStatus::Archived)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Service $record) => app(ArchiveService::class)->handle($record)),
                EditAction::make()
                    ->after(fn (Service $record) => app(ServiceService::class)->persisted($record)),
                DeleteAction::make()
                    ->after(fn () => app(ServiceService::class)->forget()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->after(fn () => app(ServiceService::class)->forget()),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageServices::route('/'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function mediaImageOptions(): array
    {
        return collect(config('zyntech-media.images', []))
            ->mapWithKeys(fn (array $image, string $key): array => [
                $key => str_replace('_', ' ', $key),
            ])
            ->all();
    }
}
