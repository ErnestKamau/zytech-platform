<?php

namespace App\Filament\Resources\Sessions;

use App\Core\Filament\BaseResource;
use App\Filament\Resources\Sessions\Pages\ManageSessions;
use App\Models\Session;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class SessionResource extends BaseResource
{
    protected static ?string $model = Session::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedComputerDesktop;

    protected static string|\UnitEnum|null $navigationGroup = 'Identity';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'id';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('User')
                    ->placeholder('Guest')
                    ->searchable(),
                TextColumn::make('ip_address')
                    ->label('IP'),
                TextColumn::make('user_agent')
                    ->label('Device')
                    ->formatStateUsing(fn (?string $state): string => Str::limit((string) $state, 60)),
                TextColumn::make('last_activity')
                    ->label('Last activity')
                    ->formatStateUsing(fn (?int $state): string => $state
                        ? Carbon::createFromTimestamp($state)->diffForHumans()
                        : '—'),
            ])
            ->recordActions([
                DeleteAction::make()->label('Revoke'),
            ])
            ->defaultSort('last_activity', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSessions::route('/'),
        ];
    }
}
