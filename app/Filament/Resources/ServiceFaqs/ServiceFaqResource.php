<?php

namespace App\Filament\Resources\ServiceFaqs;

use App\Core\Filament\BaseResource;
use App\Domains\Service\Services\ServiceService;
use App\Filament\Resources\ServiceFaqs\Pages\ManageServiceFaqs;
use App\Models\ServiceFaq;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ServiceFaqResource extends BaseResource
{
    protected static ?string $model = ServiceFaq::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected static string|\UnitEnum|null $navigationGroup = 'Services';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'service FAQ';

    protected static ?string $navigationLabel = 'FAQs';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('service_id')
                    ->relationship('service', 'title')
                    ->required()
                    ->searchable(),
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
                TextColumn::make('service.title')->label('Service')->searchable(),
                TextColumn::make('question')->searchable()->limit(60),
                IconColumn::make('is_published')->boolean(),
            ])
            ->filters([
                SelectFilter::make('service_id')
                    ->relationship('service', 'title')
                    ->label('Service'),
            ])
            ->recordActions([
                EditAction::make()
                    ->after(fn (ServiceFaq $record) => app(ServiceService::class)->forget($record->service?->slug)),
                DeleteAction::make()
                    ->after(fn () => app(ServiceService::class)->forget()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->after(fn () => app(ServiceService::class)->forget()),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageServiceFaqs::route('/'),
        ];
    }
}
