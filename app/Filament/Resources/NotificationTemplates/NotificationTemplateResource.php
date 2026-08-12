<?php

namespace App\Filament\Resources\NotificationTemplates;

use App\Core\Enums\NotificationChannel;
use App\Core\Filament\BaseResource;
use App\Domains\Communication\Services\TemplateService;
use App\Filament\Resources\NotificationTemplates\Pages\ManageNotificationTemplates;
use App\Models\NotificationTemplate;
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
use Filament\Tables\Table;

class NotificationTemplateResource extends BaseResource
{
    protected static ?string $model = NotificationTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static string|\UnitEnum|null $navigationGroup = 'Communication';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Email templates';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('key')->required()->unique(ignoreRecord: true),
            TextInput::make('name')->required(),
            Select::make('channel')->options(collect(NotificationChannel::cases())->mapWithKeys(
                fn (NotificationChannel $channel): array => [$channel->value => $channel->label()]
            ))->required(),
            TextInput::make('subject')->required()->columnSpanFull(),
            Textarea::make('body')->required()->rows(6)->columnSpanFull()
                ->helperText('Use {{name}}, {{reference}}, {{message}} placeholders.'),
            Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('key'),
                TextColumn::make('channel')->badge()->formatStateUsing(
                    fn (NotificationChannel $state): string => $state->label()
                ),
                IconColumn::make('is_active')->boolean(),
            ])
            ->defaultSort('name')
            ->recordActions([
                EditAction::make()->after(fn () => app(TemplateService::class)->forget()),
                DeleteAction::make()->after(fn () => app(TemplateService::class)->forget()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageNotificationTemplates::route('/')];
    }
}
