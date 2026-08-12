<?php

namespace App\Filament\Resources\Quotations;

use App\Core\Enums\QuotationStatus;
use App\Core\Enums\QuotationType;
use App\Core\Filament\BaseResource;
use App\Domains\Quotation\Actions\ApproveQuotation;
use App\Domains\Quotation\Actions\SendQuotation;
use App\Domains\Quotation\Services\QuotationService;
use App\Filament\Resources\Quotations\Pages\ManageQuotations;
use App\Models\Quotation;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
        return $schema->components([
            TextInput::make('reference_number')->disabled(),
            TextInput::make('title')->required()->columnSpanFull(),
            Select::make('quotation_request_id')->relationship('request', 'reference_number')->searchable(),
            Select::make('type')->options(collect(QuotationType::cases())->mapWithKeys(
                fn (QuotationType $type): array => [$type->value => $type->label()]
            ))->required(),
            Select::make('status')->options(collect(QuotationStatus::cases())->mapWithKeys(
                fn (QuotationStatus $status): array => [$status->value => $status->label()]
            ))->required(),
            DatePicker::make('valid_until'),
            TextInput::make('discount_amount')->numeric()->prefix('KES')->default(0),
            Textarea::make('notes')->rows(3)->columnSpanFull(),
            Textarea::make('terms')->rows(4)->columnSpanFull(),
            Repeater::make('sections')->relationship()->schema([
                TextInput::make('title')->required(),
                Textarea::make('description')->rows(2),
            ])->orderColumn('sort_order')->collapsible()->columnSpanFull(),
            Repeater::make('items')->relationship()->schema([
                Select::make('quotation_section_id')->relationship('section', 'title'),
                TextInput::make('label')->required(),
                Textarea::make('description')->rows(2),
                TextInput::make('quantity')->numeric()->default(1),
                TextInput::make('unit')->placeholder('sqm, item, day'),
                TextInput::make('unit_price')->numeric()->prefix('KES')->default(0),
                Toggle::make('is_optional')->default(false),
            ])->orderColumn('sort_order')->collapsible()->columnSpanFull(),
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
                Action::make('approve')
                    ->visible(fn (Quotation $record): bool => in_array($record->status, [QuotationStatus::Draft, QuotationStatus::Reviewing], true))
                    ->requiresConfirmation()
                    ->action(fn (Quotation $record) => app(ApproveQuotation::class)->handle($record)),
                Action::make('send')
                    ->visible(fn (Quotation $record): bool => in_array($record->status, [QuotationStatus::Preparing, QuotationStatus::Reviewing], true))
                    ->requiresConfirmation()
                    ->action(fn (Quotation $record) => app(SendQuotation::class)->handle($record)),
                EditAction::make()->after(fn (Quotation $record) => app(QuotationService::class)->recalculate($record)),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageQuotations::route('/')];
    }
}
