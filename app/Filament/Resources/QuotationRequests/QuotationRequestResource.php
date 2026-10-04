<?php

namespace App\Filament\Resources\QuotationRequests;

use App\Core\Enums\ProjectType;
use App\Core\Enums\QuotationStatus;
use App\Core\Filament\BaseResource;
use App\Core\Filament\BusinessHistoryAction;
use App\Domains\Quotation\Actions\CreateQuotationFromRequest;
use App\Filament\Resources\QuotationRequests\Pages\ManageQuotationRequests;
use App\Filament\Resources\QuotationRequests\Pages\ViewQuotationRequest;
use App\Filament\Resources\Quotations\QuotationResource;
use App\Models\Quotation;
use App\Models\QuotationRequest;
use App\Models\QuotationRequestAttachment;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Number;

class QuotationRequestResource extends BaseResource
{
    protected static ?string $model = QuotationRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInbox;

    protected static string|\UnitEnum|null $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Requests';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('reference_number')->disabled(),
            TextInput::make('full_name')->required(),
            TextInput::make('email')->email()->required(),
            TextInput::make('phone'),
            Select::make('client_id')->relationship('client', 'name')->searchable(),
            Select::make('project_type')->options(collect(ProjectType::cases())->mapWithKeys(
                fn (ProjectType $type): array => [$type->value => $type->label()]
            ))->required(),
            TextInput::make('county'),
            TextInput::make('location')->label('Project location'),
            Select::make('status')->options(collect(QuotationStatus::cases())->mapWithKeys(
                fn (QuotationStatus $status): array => [$status->value => $status->label()]
            ))->required(),
            Textarea::make('description')->label('Project description')->rows(4)->columnSpanFull(),
            Textarea::make('internal_notes')->rows(3)->columnSpanFull(),
            CheckboxList::make('services')->label('Required services')->relationship('services', 'title')->columns(2)->columnSpanFull(),
            Repeater::make('items')
                ->relationship()
                ->schema([
                    Select::make('product_id')->relationship('product', 'title')->searchable(),
                    Select::make('product_variant_id')->relationship('variant', 'title')->searchable(),
                    TextInput::make('description')->required(),
                    TextInput::make('quantity')->numeric()->default(1)->required(),
                    TextInput::make('unit_snapshot')->label('Unit'),
                    TextInput::make('sort_order')->numeric()->default(0),
                ])
                ->columns(3)
                ->columnSpanFull()
                ->defaultItems(0)
                ->collapsible(),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Client')
                ->columns(4)
                ->schema([
                    TextEntry::make('full_name')->label('Client'),
                    TextEntry::make('email')->label('Client email')->copyable(),
                    TextEntry::make('phone')->placeholder('—')->copyable(),
                    TextEntry::make('client.name')->label('Client account')->placeholder('Not linked'),
                ]),
            Section::make('Project')
                ->columns(4)
                ->schema([
                    TextEntry::make('reference_number')->label('Reference')->copyable(),
                    TextEntry::make('project_type')->badge()->formatStateUsing(
                        fn (ProjectType $state): string => $state->label()
                    ),
                    TextEntry::make('county')->placeholder('—'),
                    TextEntry::make('location')->label('Project location')->placeholder('—'),
                    TextEntry::make('status')->badge()->formatStateUsing(
                        fn (QuotationStatus $state): string => $state->label()
                    ),
                    TextEntry::make('submitted_at')->dateTime(),
                    TextEntry::make('services.title')->label('Required services')->badge()->placeholder('None selected')->columnSpan(2),
                    TextEntry::make('description')->label('Project description')->columnSpanFull()->extraAttributes(['style' => 'white-space: pre-line']),
                ]),
            Section::make('Attachments')
                ->visible(fn (QuotationRequest $record): bool => $record->attachments()->exists())
                ->schema([
                    RepeatableEntry::make('attachments')
                        ->hiddenLabel()
                        ->columns(2)
                        ->schema([
                            TextEntry::make('original_name')
                                ->hiddenLabel()
                                ->icon(Heroicon::OutlinedPaperClip)
                                ->color('primary')
                                ->url(fn (QuotationRequestAttachment $record): string => route(
                                    'filament.admin.quotation-request-attachments.download',
                                    ['attachment' => $record],
                                )),
                            TextEntry::make('size_bytes')
                                ->hiddenLabel()
                                ->formatStateUsing(fn (int $state): string => Number::fileSize($state)),
                        ]),
                ]),
            Section::make('Quotation')
                ->visible(fn (QuotationRequest $record): bool => $record->quotation !== null)
                ->columns(4)
                ->schema([
                    TextEntry::make('quotation.reference_number')->label('Reference'),
                    TextEntry::make('quotation.status')->label('Status')->badge()->formatStateUsing(
                        fn (QuotationStatus $state): string => $state->label()
                    ),
                    TextEntry::make('quotation.total_amount')->label('Total (incl. VAT)')->money('KES'),
                    TextEntry::make('quotation.valid_until')->label('Valid until')->date()->placeholder('—'),
                ]),
            Section::make('Internal notes')
                ->visible(fn (QuotationRequest $record): bool => filled($record->internal_notes))
                ->schema([
                    TextEntry::make('internal_notes')->hiddenLabel()->extraAttributes(['style' => 'white-space: pre-line']),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference_number')->label('Reference')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('full_name')->label('Client')->searchable()->sortable(),
                TextColumn::make('email')->label('Client email')->searchable()->copyable(),
                TextColumn::make('phone')->searchable()->placeholder('—'),
                TextColumn::make('county')->searchable()->sortable()->placeholder('—'),
                TextColumn::make('location')->label('Project location')->searchable()->placeholder('—')->limit(30),
                TextColumn::make('description')
                    ->label('Project description')
                    ->limit(50)
                    ->tooltip(fn (QuotationRequest $record): string => $record->description)
                    ->wrap(),
                TextColumn::make('services.title')->label('Required services')->badge()->placeholder('—'),
                TextColumn::make('status')->badge()->formatStateUsing(
                    fn (QuotationStatus $state): string => $state->label()
                ),
                TextColumn::make('submitted_at')->label('Submitted at')->dateTime()->sortable(),
            ])
            ->defaultSort('submitted_at', 'desc')
            ->recordUrl(fn (QuotationRequest $record): string => static::getUrl('view', ['record' => $record]))
            ->recordActions([
                ViewAction::make(),
                BusinessHistoryAction::make(metaKeys: ['quotation_request_id']),
                static::prepareQuotationAction(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function prepareQuotationAction(): Action
    {
        return Action::make('create_quotation')
            ->label('Prepare quotation')
            ->icon(Heroicon::OutlinedDocumentPlus)
            ->visible(fn (QuotationRequest $record): bool => $record->quotation === null && Gate::allows('create', Quotation::class))
            ->requiresConfirmation()
            ->modalDescription('A draft quotation will be created with the requested services as line items. You can then set prices, VAT and terms.')
            ->action(function (QuotationRequest $record) {
                $quotation = app(CreateQuotationFromRequest::class)->handle($record);

                Notification::make()
                    ->success()
                    ->title('Draft quotation created')
                    ->body($quotation->reference_number.' is ready for pricing.')
                    ->send();

                return redirect(QuotationResource::getUrl('edit', ['record' => $quotation]));
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageQuotationRequests::route('/'),
            'view' => ViewQuotationRequest::route('/{record}'),
        ];
    }
}
