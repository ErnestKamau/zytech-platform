<?php

namespace App\Filament\Resources\Quotations;

use App\Core\Enums\QuotationStatus;
use App\Core\Enums\QuotationType;
use App\Core\Filament\BaseResource;
use App\Core\Filament\BusinessHistoryAction;
use App\Domains\Quotation\Services\PricingService;
use App\Domains\Quotation\Services\QuotationService;
use App\Filament\Resources\Quotations\Pages\EditQuotation;
use App\Filament\Resources\Quotations\Pages\ManageQuotations;
use App\Models\Quotation;
use BackedEnum;
use DomainException;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QuotationResource extends BaseResource
{
    protected static ?string $model = Quotation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|\UnitEnum|null $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Client request')
                ->description('What the client asked for on the website.')
                ->visible(fn (?Quotation $record): bool => $record?->request !== null)
                ->collapsible()
                ->columns(3)
                ->schema([
                    TextEntry::make('request.full_name')->label('Client'),
                    TextEntry::make('request.email')->label('Email')->copyable(),
                    TextEntry::make('request.phone')->label('Phone')->placeholder('—'),
                    TextEntry::make('request.county')->label('County')->placeholder('—'),
                    TextEntry::make('request.location')->label('Project location')->placeholder('—'),
                    TextEntry::make('request.services.title')->label('Required services')->badge()->placeholder('None selected'),
                    TextEntry::make('request.description')->label('Project description')->columnSpanFull(),
                ]),

            Section::make('Quotation details')
                ->columns(3)
                ->schema([
                    TextInput::make('title')->required()->columnSpan(2),
                    TextInput::make('reference_number')->disabled()->dehydrated(false),
                    Select::make('status')->options(collect(QuotationStatus::cases())->mapWithKeys(
                        fn (QuotationStatus $status): array => [$status->value => $status->label()]
                    ))->required(),
                    Select::make('type')->options(collect(QuotationType::cases())->mapWithKeys(
                        fn (QuotationType $type): array => [$type->value => $type->label()]
                    ))->required(),
                    DatePicker::make('valid_until'),
                    Select::make('quotation_request_id')
                        ->label('Request')
                        ->relationship('request', 'reference_number')
                        ->searchable(),
                ]),

            Section::make('Line items')
                ->description('Quantities and prices are excluding VAT. Optional items are shown on the PDF but not added to the total.')
                ->schema([
                    Repeater::make('items')
                        ->hiddenLabel()
                        ->relationship()
                        ->orderColumn('sort_order')
                        ->defaultItems(0)
                        ->addActionLabel('Add line item')
                        ->live()
                        ->table([
                            TableColumn::make('Item')->markAsRequired(),
                            TableColumn::make('Description'),
                            TableColumn::make('Qty')->width('6rem'),
                            TableColumn::make('Unit')->width('7rem'),
                            TableColumn::make('Unit price')->width('10rem'),
                            TableColumn::make('Amount')->width('9rem'),
                            TableColumn::make('Optional')->width('5rem'),
                        ])
                        ->schema([
                            TextInput::make('label')->required()->maxLength(255),
                            TextInput::make('description'),
                            TextInput::make('quantity')->numeric()->minValue(0)->default(1)->required()->live(onBlur: true),
                            TextInput::make('unit')->placeholder('sqm, item'),
                            TextInput::make('unit_price')->numeric()->minValue(0)->default(0)->required()->live(onBlur: true),
                            TextEntry::make('amount')->state(fn (Get $get): string => number_format(
                                app(PricingService::class)->lineTotal((float) $get('quantity'), (float) $get('unit_price')),
                                2,
                            )),
                            Toggle::make('is_optional')->default(false)->live(),
                        ]),
                ]),

            Section::make('Pricing & tax')
                ->schema([
                    Grid::make(4)->schema([
                        TextInput::make('tax_rate')
                            ->label('VAT rate')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%')
                            ->default(PricingService::DEFAULT_TAX_RATE)
                            ->required()
                            ->live(onBlur: true),
                        TextInput::make('discount_amount')
                            ->label('Discount')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('KES')
                            ->default(0)
                            ->helperText('Applied before VAT.')
                            ->live(onBlur: true),
                    ]),
                    Grid::make(4)->schema([
                        TextEntry::make('summary_subtotal')->label('Subtotal')
                            ->state(fn (Get $get): string => static::formatLiveTotal($get, 'subtotal')),
                        TextEntry::make('summary_discount')->label('Discount')
                            ->state(fn (Get $get): string => static::formatLiveTotal($get, 'discount_amount')),
                        TextEntry::make('summary_tax')->label('VAT')
                            ->state(fn (Get $get): string => static::formatLiveTotal($get, 'tax_amount')),
                        TextEntry::make('summary_total')->label('Total')->weight('bold')->size('lg')
                            ->state(fn (Get $get): string => static::formatLiveTotal($get, 'total_amount')),
                    ]),
                ]),

            Section::make('Terms & notes')
                ->description('Both appear on the PDF.')
                ->schema([
                    Textarea::make('terms')
                        ->label('Terms & conditions')
                        ->rows(8)
                        ->helperText('One clause per line; line breaks are kept on the PDF.'),
                    Textarea::make('notes')->label('Notes to client')->rows(3),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference_number')->searchable(),
                TextColumn::make('title')->searchable()->limit(40),
                TextColumn::make('request.reference_number')->label('Request'),
                TextColumn::make('status')->badge()->formatStateUsing(
                    fn (QuotationStatus $state): string => $state->label()
                ),
                TextColumn::make('total_amount')->money('KES'),
                TextColumn::make('valid_until')->date(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->recordActions([
                BusinessHistoryAction::make(metaKeys: ['quotation_id']),
                Action::make('preview_pdf')
                    ->label('PDF')
                    ->icon(Heroicon::OutlinedDocumentMagnifyingGlass)
                    ->color('gray')
                    ->url(fn (Quotation $record): string => static::pdfPreviewUrl($record), shouldOpenInNewTab: true),
                Action::make('send_to_client')
                    ->label('Send to client')
                    ->icon(Heroicon::OutlinedPaperAirplane)
                    ->visible(fn (Quotation $record): bool => in_array($record->status, QuotationService::SENDABLE_STATUSES, true))
                    ->requiresConfirmation()
                    ->modalDescription('The client is emailed and can accept or request a revision in their portal.')
                    ->action(function (Quotation $record): void {
                        try {
                            app(QuotationService::class)->sendToClient($record);
                        } catch (DomainException $exception) {
                            Notification::make()->danger()->title('Not sent')->body($exception->getMessage())->send();

                            return;
                        }

                        Notification::make()->success()->title('Quotation sent')->send();
                    }),
                Action::make('prepare_revision')
                    ->label('Prepare revision')
                    ->visible(fn (Quotation $record): bool => $record->status === QuotationStatus::RevisionRequested)
                    ->requiresConfirmation()
                    ->action(function (Quotation $record): void {
                        $record->forceFill(['status' => QuotationStatus::Preparing])->save();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function pdfPreviewUrl(Quotation $quotation): string
    {
        return route('filament.admin.quotations.pdf-preview', ['quotation' => $quotation]);
    }

    private static function formatLiveTotal(Get $get, string $key): string
    {
        $totals = app(PricingService::class)->summarize(
            array_values((array) ($get('items') ?? [])),
            (float) $get('tax_rate'),
            (float) $get('discount_amount'),
        );

        return 'KES '.number_format($totals[$key], 2);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageQuotations::route('/'),
            'edit' => EditQuotation::route('/{record}/edit'),
        ];
    }
}
