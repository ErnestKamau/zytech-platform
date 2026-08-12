<?php

namespace App\Filament\Resources\Certifications;

use App\Core\Enums\CertificationStatus;
use App\Core\Filament\BaseResource;
use App\Domains\Company\Events\CertificationUpdated;
use App\Domains\Company\Services\CompanyService;
use App\Filament\Resources\Certifications\Pages\ManageCertifications;
use App\Models\Certification;
use App\Models\Company;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CertificationResource extends BaseResource
{
    protected static ?string $model = Certification::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCheck;

    protected static string|\UnitEnum|null $navigationGroup = 'Company';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('company_id')->default(fn () => Company::query()->value('id')),
                TextInput::make('name')->required(),
                TextInput::make('issuer'),
                DatePicker::make('issued_on'),
                DatePicker::make('expires_on'),
                Select::make('status')
                    ->options(collect(CertificationStatus::cases())->mapWithKeys(
                        fn (CertificationStatus $status): array => [$status->value => $status->label()]
                    ))
                    ->required()
                    ->default(CertificationStatus::Active->value),
                TextInput::make('document_url')->url(),
                TextInput::make('sort_order')->numeric()->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('issuer'),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (CertificationStatus $state): string => $state->label()),
                TextColumn::make('expires_on')->date(),
            ])
            ->recordActions([
                EditAction::make()
                    ->after(function (Certification $record): void {
                        event(new CertificationUpdated($record));
                    }),
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
            'index' => ManageCertifications::route('/'),
        ];
    }
}
