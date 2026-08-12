<?php

namespace App\Filament\Resources\Faqs;

use App\Core\Filament\BaseResource;
use App\Domains\Company\Services\CompanyService;
use App\Filament\Resources\Faqs\Pages\ManageFaqs;
use App\Models\Company;
use App\Models\Faq;
use BackedEnum;
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

class FaqResource extends BaseResource
{
    protected static ?string $model = Faq::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected static string|\UnitEnum|null $navigationGroup = 'Company';

    protected static ?int $navigationSort = 8;

    protected static ?string $navigationLabel = 'FAQs';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('company_id')->default(fn () => Company::query()->value('id')),
                TextInput::make('question')->required()->columnSpanFull(),
                Textarea::make('answer')->required()->rows(4)->columnSpanFull(),
                Toggle::make('is_published')->default(true),
                TextInput::make('sort_order')->numeric()->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('question')->searchable()->limit(60),
                IconColumn::make('is_published')->boolean(),
            ])
            ->recordActions([
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
            'index' => ManageFaqs::route('/'),
        ];
    }
}
