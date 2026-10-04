<?php

namespace App\Filament\Resources\Quotations;

use App\Core\Enums\QuotationStatus;
use App\Core\Enums\QuotationType;
use App\Core\Filament\BaseResource;
use App\Core\Filament\BusinessHistoryAction;
use App\Domains\Company\Services\CompanyService;
use App\Domains\Quotation\Services\PricingService;
use App\Domains\Quotation\Services\QuotationService;
use App\Filament\Resources\Quotations\Pages\EditQuotation;
use App\Filament\Resources\Quotations\Pages\ManageQuotations;
use App\Models\Quotation;
use App\Models\QuotationSection;
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
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class QuotationResource extends BaseResource
{
    protected static ?string $model = Quotation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|\UnitEnum|null $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(3)->components([
            Wizard::make([
                Step::make('Client & request')
                    ->icon(Heroicon::OutlinedUser)
                    ->schema([
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
                                TextInput::make('title')->required()->live(onBlur: true)->columnSpan(2),
                                TextInput::make('reference_number')->disabled()->dehydrated(false),
                                Select::make('status')->options(collect(QuotationStatus::cases())->mapWithKeys(
                                    fn (QuotationStatus $status): array => [$status->value => $status->label()]
                                ))->required(),
                                Select::make('type')->options(collect(QuotationType::cases())->mapWithKeys(
                                    fn (QuotationType $type): array => [$type->value => $type->label()]
                                ))->required(),
                                DatePicker::make('valid_until')->live(),
                                Select::make('quotation_request_id')
                                    ->label('Request')
                                    ->relationship('request', 'reference_number')
                                    ->searchable(),
                            ]),
                    ]),

                Step::make('Line items & sections')
                    ->icon(Heroicon::OutlinedListBullet)
                    ->schema([
                        Section::make('Line items')
                            ->description('Group items under a section so the PDF reads like a scoped proposal. Quantities and prices are excluding VAT. Optional items appear on the PDF but are not added to the total.')
                            ->schema([
                                Repeater::make('items')
                                    ->hiddenLabel()
                                    ->relationship()
                                    ->orderColumn('sort_order')
                                    ->defaultItems(0)
                                    ->addActionLabel('Add line item')
                                    ->live()
                                    ->table([
                                        TableColumn::make('Section')->width('10rem'),
                                        TableColumn::make('Item')->markAsRequired(),
                                        TableColumn::make('Description'),
                                        TableColumn::make('Qty')->width('6rem'),
                                        TableColumn::make('Unit')->width('7rem'),
                                        TableColumn::make('Unit price')->width('10rem'),
                                        TableColumn::make('Amount')->width('9rem'),
                                        TableColumn::make('Optional')->width('5rem'),
                                    ])
                                    ->schema([
                                        Select::make('quotation_section_id')
                                            ->label('Section')
                                            ->relationship(
                                                name: 'section',
                                                titleAttribute: 'title',
                                                modifyQueryUsing: fn (Builder $query, $livewire): Builder => $query
                                                    ->where('quotation_id', $livewire->getRecord()?->id),
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm([
                                                TextInput::make('title')->required()->maxLength(255),
                                            ])
                                            ->createOptionUsing(function (array $data, $livewire): string {
                                                return QuotationSection::query()->create([
                                                    'quotation_id' => $livewire->getRecord()?->id,
                                                    'title' => $data['title'],
                                                    'sort_order' => $livewire->getRecord()?->sections()->count() ?? 0,
                                                ])->getKey();
                                            }),
                                        TextInput::make('label')->required()->maxLength(255)->live(onBlur: true),
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
                    ]),

                Step::make('Pricing, tax & terms')
                    ->icon(Heroicon::OutlinedCalculator)
                    ->schema([
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
                                    ->live(onBlur: true)
                                    ->helperText('One clause per line; line breaks are kept on the PDF.'),
                                Textarea::make('notes')->label('Notes to client')->rows(3)->live(onBlur: true),
                            ]),
                    ]),
            ])
                ->persistStepInQueryString()
                ->columnSpan(2),

            Section::make('Live preview')
                ->description('Updates as you type. This mirrors the PDF the client will receive.')
                ->columnSpan(1)
                ->schema([
                    TextEntry::make('live_preview_sheet')
                        ->hiddenLabel()
                        ->html()
                        ->state(fn (Get $get, ?Quotation $record): string => static::renderLivePreview($get, $record)),
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
                Action::make('view_document')
                    ->label('View')
                    ->icon(Heroicon::OutlinedDocumentMagnifyingGlass)
                    ->color('gray')
                    ->url(fn (Quotation $record): string => route('filament.admin.documents.quotation', ['quotation' => $record]), shouldOpenInNewTab: true),
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

    private static function renderLivePreview(Get $get, ?Quotation $record): string
    {
        $company = app(CompanyService::class)->current();
        $items = array_values((array) ($get('items') ?? []));
        $totals = app(PricingService::class)->summarize($items, (float) $get('tax_rate'), (float) $get('discount_amount'));
        $sections = $record?->sections()->pluck('title', 'id') ?? collect();

        $clientName = $record?->client?->name ?? $record?->request?->full_name ?? '—';
        $title = $get('title') ?: ($record?->title ?? 'Untitled quotation');
        $reference = $record?->reference_number ?? 'Draft';

        $rows = '';
        foreach ($items as $item) {
            $label = e($item['label'] ?? '');
            $qty = rtrim(rtrim(number_format((float) ($item['quantity'] ?? 0), 2), '0'), '.');
            $unit = e($item['unit'] ?? '');
            $amount = number_format(app(PricingService::class)->lineTotal((float) ($item['quantity'] ?? 0), (float) ($item['unit_price'] ?? 0)), 2);
            $optionalBadge = ! empty($item['is_optional']) ? ' <span style="color:#b45309;font-size:9px;">(optional)</span>' : '';
            $sectionTitle = isset($item['quotation_section_id']) ? e($sections->get($item['quotation_section_id'], '')) : '';

            $rows .= '<tr>'
                .'<td style="padding:5px 4px;border-bottom:1px solid #ece6df;font-size:10px;color:#6b6560;">'.($sectionTitle !== '' ? $sectionTitle : '—').'</td>'
                .'<td style="padding:5px 4px;border-bottom:1px solid #ece6df;font-size:11px;">'.$label.$optionalBadge.'</td>'
                .'<td style="padding:5px 4px;border-bottom:1px solid #ece6df;font-size:11px;text-align:right;">'.$qty.' '.$unit.'</td>'
                .'<td style="padding:5px 4px;border-bottom:1px solid #ece6df;font-size:11px;text-align:right;">'.$amount.'</td>'
                .'</tr>';
        }

        if ($rows === '') {
            $rows = '<tr><td colspan="4" style="padding:14px 4px;text-align:center;color:#6b6560;font-size:11px;">No line items yet.</td></tr>';
        }

        $discountRow = (float) $totals['discount_amount'] > 0
            ? '<tr><td style="padding:3px 4px;font-size:11px;">Discount</td><td></td><td></td><td style="padding:3px 4px;font-size:11px;text-align:right;">− KES '.number_format($totals['discount_amount'], 2).'</td></tr>'
            : '';

        $subtotalFormatted = number_format($totals['subtotal'], 2);
        $taxFormatted = number_format($totals['tax_amount'], 2);
        $totalFormatted = number_format($totals['total_amount'], 2);

        return <<<HTML
            <div style="border:1px solid #e4ded5;border-radius:6px;background:#fff;padding:16px;font-family:inherit;">
                <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:10px;">
                    <div>
                        <div style="font-weight:700;color:#3b4b31;font-size:13px;">{$company?->name}</div>
                        <div style="color:#6b6560;font-size:10px;">{$company?->email}</div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-weight:700;letter-spacing:.05em;color:#3b4b31;font-size:11px;">QUOTATION</div>
                        <div style="color:#6b6560;font-size:10px;">{$reference}</div>
                    </div>
                </div>
                <div style="border-top:2px solid #5c7349;margin-bottom:10px;"></div>
                <div style="font-size:10px;color:#6b6560;margin-bottom:2px;">PREPARED FOR</div>
                <div style="font-weight:600;font-size:12px;margin-bottom:10px;">{$clientName}</div>
                <div style="font-weight:600;font-size:13px;margin-bottom:8px;">{$title}</div>
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th style="text-align:left;font-size:9px;text-transform:uppercase;color:#4a443e;padding:5px 4px;border-bottom:1px solid #ddd5cc;">Section</th>
                            <th style="text-align:left;font-size:9px;text-transform:uppercase;color:#4a443e;padding:5px 4px;border-bottom:1px solid #ddd5cc;">Item</th>
                            <th style="text-align:right;font-size:9px;text-transform:uppercase;color:#4a443e;padding:5px 4px;border-bottom:1px solid #ddd5cc;">Qty</th>
                            <th style="text-align:right;font-size:9px;text-transform:uppercase;color:#4a443e;padding:5px 4px;border-bottom:1px solid #ddd5cc;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>{$rows}</tbody>
                </table>
                <table style="width:60%;margin-left:auto;margin-top:10px;">
                    <tr><td style="padding:3px 4px;font-size:11px;">Subtotal</td><td></td><td></td><td style="padding:3px 4px;font-size:11px;text-align:right;">KES {$subtotalFormatted}</td></tr>
                    {$discountRow}
                    <tr><td style="padding:3px 4px;font-size:11px;">VAT</td><td></td><td></td><td style="padding:3px 4px;font-size:11px;text-align:right;">KES {$taxFormatted}</td></tr>
                    <tr style="border-top:2px solid #5c7349;"><td style="padding:6px 4px;font-weight:700;color:#3b4b31;font-size:12px;">Total</td><td></td><td></td><td style="padding:6px 4px;font-weight:700;color:#3b4b31;font-size:12px;text-align:right;">KES {$totalFormatted}</td></tr>
                </table>
            </div>
            HTML;
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageQuotations::route('/'),
            'edit' => EditQuotation::route('/{record}/edit'),
        ];
    }
}
