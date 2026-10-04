<?php

namespace App\Filament\Resources\HomepageSlides;

use App\Core\Filament\BaseResource;
use App\Domains\Homepage\Actions\SyncHomepageSlideMedia;
use App\Domains\Homepage\Services\HomepageSlideService;
use App\Filament\Resources\HomepageSlides\Pages\ManageHomepageSlides;
use App\Models\HomepageSlide;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HomepageSlideResource extends BaseResource
{
    protected static ?string $model = HomepageSlide::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|\UnitEnum|null $navigationGroup = 'Website';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'homepage slide';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('headline')->maxLength(255),
                TextInput::make('eyebrow')->maxLength(255),
                TextInput::make('alt_text')
                    ->label('Alt text')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Required for accessibility — describe the slide even though it is decorative.'),
                TextInput::make('cta_label')->maxLength(255),
                TextInput::make('cta_url')->url()->maxLength(255),
                Toggle::make('is_active')->default(true),
                TextInput::make('sort_order')->numeric()->default(0),
                FileUpload::make('media_upload')
                    ->label('Slide video or image')
                    ->disk('public')
                    ->directory('homepage-slide-tmp')
                    ->acceptedFileTypes(['video/mp4', 'video/quicktime', 'image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(102400)
                    ->helperText('Uploading replaces the current slide asset.'),
                FileUpload::make('poster_upload')
                    ->label('Poster image (for video slides)')
                    ->disk('public')
                    ->directory('homepage-slide-tmp')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(20480)
                    ->helperText('Shown while the video loads and as the fallback image for visitors with reduced motion enabled.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('headline')->placeholder('—'),
                TextColumn::make('eyebrow')->placeholder('—')->toggleable(),
                IconColumn::make('is_active')->boolean(),
                TextColumn::make('sort_order'),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make()->after(fn (array $data, HomepageSlide $record) => app(SyncHomepageSlideMedia::class)->handle($record, $data)),
                DeleteAction::make()->after(fn () => app(HomepageSlideService::class)->forget()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->after(fn () => app(HomepageSlideService::class)->forget()),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageHomepageSlides::route('/')];
    }
}
