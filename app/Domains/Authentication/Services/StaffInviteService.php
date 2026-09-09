<?php

namespace App\Domains\Authentication\Services;

use App\Core\Enums\RoleType;
use App\Core\Enums\UserType;
use App\Core\Services\BaseService;
use App\Domains\Communication\Mail\StaffInviteMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use RuntimeException;

final class StaffInviteService extends BaseService
{
    public const INVITE_TTL_DAYS = 7;

    /**
     * @return non-empty-string Plaintext token (only available at send time)
     */
    public function send(User $user): string
    {
        $this->ensureAdminRole($user);

        if (! $user->canAccessPanel(filament()->getPanel('admin'))) {
            throw new RuntimeException('Only admin-capable users can receive a staff invite.');
        }

        $token = Str::random(64);

        $user->forceFill([
            'admin_invite_token' => hash('sha256', $token),
            'admin_invite_sent_at' => now(),
            'admin_invite_expires_at' => now()->addDays(self::INVITE_TTL_DAYS),
            'admin_onboarded_at' => null,
        ])->save();

        Mail::mailer(config('mail.default'))->to($user->email)->send(new StaffInviteMail(
            userName: (string) $user->name,
            inviteUrl: route('admin.invite.accept', ['token' => $token]),
        ));

        return $token;
    }

    private function ensureAdminRole(User $user): void
    {
        if ($user->canAccessPanel(filament()->getPanel('admin'))) {
            return;
        }

        $role = match ($user->type) {
            UserType::Administrator => RoleType::Administrator->value,
            UserType::Staff => RoleType::Staff->value,
            default => null,
        };

        if ($role !== null) {
            $user->assignRole($role);
            $user->unsetRelation('roles');
        }
    }

    public function findValidInvitee(string $token): ?User
    {
        if ($token === '') {
            return null;
        }

        return User::query()
            ->where('admin_invite_token', hash('sha256', $token))
            ->whereNotNull('admin_invite_expires_at')
            ->where('admin_invite_expires_at', '>', now())
            ->first();
    }

    public function consume(User $user): void
    {
        $user->forceFill([
            'admin_invite_token' => null,
            'admin_invite_expires_at' => null,
            'email_verified_at' => $user->email_verified_at ?? now(),
        ])->save();
    }

    public function completeOnboarding(User $user): void
    {
        $user->forceFill([
            'admin_onboarded_at' => now(),
            'admin_invite_token' => null,
            'admin_invite_expires_at' => null,
        ])->save();
    }
}
