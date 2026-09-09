<?php

namespace App\Filament\Resources\Users\Pages;

use App\Domains\Authentication\Services\StaffInviteService;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Throwable;

class ManageUsers extends ManageRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->after(function (User $record): void {
                    if (! $record->isInviteEligible()) {
                        return;
                    }

                    try {
                        app(StaffInviteService::class)->send($record);

                        Notification::make()
                            ->title('Invite email sent')
                            ->body('They can open Admin from the invite link.')
                            ->success()
                            ->send();
                    } catch (Throwable $e) {
                        Notification::make()
                            ->title('User created, but invite email failed')
                            ->body($e->getMessage())
                            ->warning()
                            ->send();
                    }
                }),
        ];
    }
}
