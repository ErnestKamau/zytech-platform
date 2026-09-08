<?php

namespace App\Filament\Resources\DomainActivities;

use App\Core\Filament\BaseResource;
use App\Filament\Resources\DomainActivities\Pages\ManageDomainActivities;
use App\Models\DomainActivity;
use BackedEnum;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DomainActivityResource extends BaseResource
{
    protected static ?string $model = DomainActivity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|\UnitEnum|null $navigationGroup = 'Clients';

    protected static ?int $navigationSort = 21;

    protected static ?string $navigationLabel = 'Activity log';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('event')->searchable(),
                TextColumn::make('subject_type')->label('Subject')->formatStateUsing(
                    fn (?string $state): string => $state ? class_basename($state) : '—'
                ),
                TextColumn::make('actor.name')->label('Actor')->placeholder('System'),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageDomainActivities::route('/'),
        ];
    }
}
