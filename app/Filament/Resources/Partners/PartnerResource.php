<?php

namespace App\Filament\Resources\Partners;

use App\Core\Filament\BaseResource;
use App\Domains\Company\Actions\ArchivePartner;
use App\Domains\Company\Services\CompanyService;
use App\Filament\Resources\Partners\Pages\ManagePartners;
use App\Models\Company;
use App\Models\Partner;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PartnerResource extends BaseResource
{
    protected static ?string $model = Partner::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static string|\UnitEnum|null $navigationGroup = 'Company';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('company_id')->default(fn () => Company::query()->value('id')),
                TextInput::make('name')->required(),
                TextInput::make('website')->url(),
                TextInput::make('logo_url')->url(),
                Textarea::make('description')->rows(3)->columnSpanFull(),
                Toggle::make('is_published')->default(true),
                TextInput::make('sort_order')->numeric()->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('website'),
                IconColumn::make('is_published')->boolean(),
                TextColumn::make('archived_at')->dateTime()->placeholder('—'),
            ])
            ->recordActions([
                Action::make('archive')
                    ->visible(fn (Partner $record): bool => ! $record->isArchived())
                    ->requiresConfirmation()
                    ->action(fn (Partner $record) => app(ArchivePartner::class)->handle($record)),
                EditAction::make()
                    ->after(fn () => app(CompanyService::class)->forget()),
                DeleteAction::make()
                    ->after(fn () => app(CompanyService::class)->forget()),
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
            'index' => ManagePartners::route('/'),
        ];
    }
}
