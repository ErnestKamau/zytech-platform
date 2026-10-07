<?php

namespace App\Filament\Resources\NotificationLogs;

use App\Core\Enums\DeliveryStatus;
use App\Core\Enums\NotificationChannel;
use App\Core\Filament\BaseResource;
use App\Filament\Resources\NotificationLogs\Pages\ListNotificationLogs;
use App\Models\NotificationLog;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NotificationLogResource extends BaseResource
{
    protected static ?string $model = NotificationLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInbox;

    protected static string|\UnitEnum|null $navigationGroup = 'Communication';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Notification log';

    protected static ?string $modelLabel = 'notification';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->label('Sent at')->dateTime()->sortable(),
                TextColumn::make('type')->searchable(),
                TextColumn::make('channel')->badge()->formatStateUsing(
                    fn (NotificationChannel $state): string => $state->label()
                ),
                TextColumn::make('status')->badge()->color(fn (DeliveryStatus $state): string => match ($state) {
                    DeliveryStatus::Sent => 'success',
                    DeliveryStatus::Failed => 'danger',
                    DeliveryStatus::Skipped => 'gray',
                    DeliveryStatus::Pending => 'warning',
                })->formatStateUsing(fn (DeliveryStatus $state): string => $state->label()),
                TextColumn::make('recipient')->searchable(),
                TextColumn::make('user.name')->label('User')->placeholder('—'),
                TextColumn::make('error')->limit(60)->placeholder('—')->tooltip(fn (NotificationLog $record): ?string => $record->error),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('channel')->options(collect(NotificationChannel::cases())->mapWithKeys(
                    fn (NotificationChannel $channel): array => [$channel->value => $channel->label()]
                )),
                SelectFilter::make('status')->options(collect(DeliveryStatus::cases())->mapWithKeys(
                    fn (DeliveryStatus $status): array => [$status->value => $status->label()]
                )),
                Filter::make('created_at')
                    ->schema([
                        \Filament\Forms\Components\DatePicker::make('from'),
                        \Filament\Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn (Builder $q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'] ?? null, fn (Builder $q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNotificationLogs::route('/'),
        ];
    }
}
