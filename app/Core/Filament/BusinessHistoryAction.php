<?php

namespace App\Core\Filament;

use App\Models\DomainActivity;
use App\Models\NotificationLog;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

final class BusinessHistoryAction
{
    /**
     * Read-only modal: DomainActivity timeline + related notification_logs.
     *
     * @param  array<string, string>  $metaKeys  meta JSON keys that identify this subject (e.g. quotation_id)
     */
    public static function make(string $name = 'history', array $metaKeys = []): Action
    {
        return Action::make($name)
            ->label('History')
            ->icon('heroicon-o-clock')
            ->modalHeading('Activity & notifications')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close')
            ->modalContent(function (Model $record) use ($metaKeys) {
                return view('filament.components.business-history', [
                    'activities' => self::activitiesFor($record),
                    'notifications' => self::notificationsFor($record, $metaKeys),
                ]);
            });
    }

    /**
     * @return Collection<int, DomainActivity>
     */
    public static function activitiesFor(Model $record): Collection
    {
        return DomainActivity::query()
            ->with('actor')
            ->where('subject_type', $record->getMorphClass())
            ->where('subject_id', $record->getKey())
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();
    }

    /**
     * @param  array<string, string>  $metaKeys
     * @return Collection<int, NotificationLog>
     */
    public static function notificationsFor(Model $record, array $metaKeys): Collection
    {
        if ($metaKeys === []) {
            return collect();
        }

        $query = NotificationLog::query()->orderByDesc('created_at')->limit(50);

        $query->where(function ($builder) use ($record, $metaKeys): void {
            foreach ($metaKeys as $key) {
                $builder->orWhere('meta->'.$key, $record->getKey());
            }
        });

        return $query->get();
    }
}
