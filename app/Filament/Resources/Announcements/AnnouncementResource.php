<?php

namespace App\Filament\Resources\Announcements;

use App\Core\Enums\AnnouncementType;
use App\Core\Filament\BaseResource;
use App\Domains\Communication\Services\AnnouncementService;
use App\Filament\Resources\Announcements\Pages\ManageAnnouncements;
use App\Models\Announcement;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AnnouncementResource extends BaseResource
{
    protected static ?string $model = Announcement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected static string|\UnitEnum|null $navigationGroup = 'Communication';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required(),
            Textarea::make('body')->required()->rows(4)->columnSpanFull(),
            Select::make('type')->options(collect(AnnouncementType::cases())->mapWithKeys(
                fn (AnnouncementType $type): array => [$type->value => $type->label()]
            ))->required(),
            Toggle::make('is_published')->default(false),
            Toggle::make('show_on_website')->default(true),
            Toggle::make('show_in_portal')->default(true),
            DateTimePicker::make('published_at'),
            DateTimePicker::make('expires_at'),
            TextInput::make('sort_order')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->searchable(),
                TextColumn::make('type')->badge()->formatStateUsing(
                    fn (AnnouncementType $state): string => $state->label()
                ),
                IconColumn::make('is_published')->boolean(),
                IconColumn::make('show_on_website')->boolean()->label('Web'),
                IconColumn::make('show_in_portal')->boolean()->label('Portal'),
                TextColumn::make('published_at')->dateTime(),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make()->after(fn () => app(AnnouncementService::class)->forget()),
                DeleteAction::make()->after(fn () => app(AnnouncementService::class)->forget()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageAnnouncements::route('/')];
    }
}
