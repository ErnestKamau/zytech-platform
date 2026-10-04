<?php

namespace App\Filament\Resources\Companies;

use App\Core\Enums\CompanyStatus;
use App\Core\Filament\BaseResource;
use App\Domains\Company\Actions\PublishCompanyProfile;
use App\Domains\Company\Actions\UpdateCompanyProfile;
use App\Filament\Resources\Companies\Pages\ManageCompanies;
use App\Models\Company;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CompanyResource extends BaseResource
{
    protected static ?string $model = Company::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|\UnitEnum|null $navigationGroup = 'Company';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'company profile';

    public static function canCreate(): bool
    {
        return Company::query()->doesntExist();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('registered_name')->maxLength(255),
                TextInput::make('tagline')->maxLength(255),
                TextInput::make('motto')->maxLength(255),
                Textarea::make('short_description')->rows(2)->columnSpanFull(),
                Textarea::make('about')->rows(4)->columnSpanFull(),
                Textarea::make('mission')->rows(3),
                Textarea::make('vision')->rows(3),
                Textarea::make('history')->rows(3)->columnSpanFull(),
                Textarea::make('why_choose_us')->rows(3)->columnSpanFull(),
                Textarea::make('core_values')
                    ->rows(4)
                    ->helperText('One value per line')
                    ->formatStateUsing(fn (mixed $state): string => is_array($state) ? implode("\n", $state) : (string) $state)
                    ->dehydrateStateUsing(fn (?string $state): array => array_values(array_filter(array_map(
                        'trim',
                        preg_split('/\r\n|\r|\n/', (string) $state) ?: [],
                    ))))
                    ->columnSpanFull(),
                TextInput::make('email')->email(),
                TextInput::make('phone'),
                TextInput::make('whatsapp'),
                TextInput::make('location'),
                TextInput::make('service_area'),
                TextInput::make('website')->url(),
                TextInput::make('registration_number'),
                TextInput::make('tax_number'),

                Section::make('Tax & banking')
                    ->description('Shown on Proforma Invoice and Tax Invoice PDFs — required for Kenyan tax-invoice compliance.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('kra_pin')->label('KRA PIN'),
                        TextInput::make('vat_number')->label('VAT number'),
                        TextInput::make('bank_name'),
                        TextInput::make('bank_account_name'),
                        TextInput::make('bank_account_number'),
                        TextInput::make('bank_branch'),
                        TextInput::make('mpesa_paybill')->label('M-Pesa paybill'),
                        TextInput::make('mpesa_account_name')->label('M-Pesa account name'),
                        FileUpload::make('logo_path')
                            ->label('Company logo')
                            ->image()
                            ->disk('public')
                            ->directory('company')
                            ->visibility('public')
                            ->imageEditor()
                            ->imagePreviewHeight('120')
                            ->columnSpanFull(),
                    ]),

                Section::make('Registered address')
                    ->columns(2)
                    ->schema([
                        TextInput::make('address_line1')->label('Address line 1')->columnSpanFull(),
                        TextInput::make('address_line2')->label('Address line 2')->columnSpanFull(),
                        TextInput::make('address_city')->label('City'),
                        TextInput::make('address_county')->label('County'),
                        TextInput::make('address_country')->label('Country')->default('Kenya'),
                    ]),

                Select::make('status')
                    ->options(collect(CompanyStatus::cases())->mapWithKeys(
                        fn (CompanyStatus $status): array => [$status->value => $status->label()]
                    ))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('tagline')->limit(40),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (CompanyStatus $state): string => $state->label()),
                TextColumn::make('email'),
            ])
            ->recordActions([
                Action::make('publish')
                    ->visible(fn (Company $record): bool => ! $record->isPublished())
                    ->requiresConfirmation()
                    ->action(fn () => app(PublishCompanyProfile::class)->handle()),
                EditAction::make()
                    ->using(fn (Company $record, array $data): Company => app(UpdateCompanyProfile::class)->handle($data)),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCompanies::route('/'),
        ];
    }
}
