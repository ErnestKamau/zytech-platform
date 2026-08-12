<?php

namespace App\Filament\Resources\Testimonials;

use App\Core\Enums\CompanyStatus;
use App\Core\Filament\BaseResource;
use App\Domains\Company\Actions\PublishTestimonial;
use App\Domains\Company\Services\CompanyService;
use App\Filament\Resources\Testimonials\Pages\ManageTestimonials;
use App\Models\Company;
use App\Models\Testimonial;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TestimonialResource extends BaseResource
{
    protected static ?string $model = Testimonial::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static string|\UnitEnum|null $navigationGroup = 'Company';

    protected static ?int $navigationSort = 7;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('company_id')->default(fn () => Company::query()->value('id')),
                TextInput::make('author_name')->required(),
                TextInput::make('author_role'),
                TextInput::make('company_name'),
                Textarea::make('quote')->required()->rows(4)->columnSpanFull(),
                Toggle::make('is_featured'),
                Select::make('status')
                    ->options(collect(CompanyStatus::cases())->mapWithKeys(
                        fn (CompanyStatus $status): array => [$status->value => $status->label()]
                    ))
                    ->required()
                    ->default(CompanyStatus::Draft->value),
                TextInput::make('sort_order')->numeric()->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('author_name')->searchable(),
                TextColumn::make('quote')->limit(40),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (CompanyStatus $state): string => $state->label()),
                IconColumn::make('is_featured')->boolean(),
            ])
            ->recordActions([
                Action::make('publish')
                    ->visible(fn (Testimonial $record): bool => ! $record->isPublished())
                    ->requiresConfirmation()
                    ->action(fn (Testimonial $record) => app(PublishTestimonial::class)->handle($record)),
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
            'index' => ManageTestimonials::route('/'),
        ];
    }
}
