<?php

namespace App\Models;

use App\Core\Enums\UserType;
use App\Core\Traits\HasActivity;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

#[Fillable([
    'name',
    'email',
    'type',
    'phone',
    'password',
    'failed_login_attempts',
    'locked_at',
    'lock_reason',
    'mfa_enabled',
    'mfa_secret',
    'preferences',
])]
#[Hidden(['password', 'remember_token', 'mfa_secret'])]
class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasActivity;

    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use HasUuids;
    use Notifiable;
    use SoftDeletes;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'type' => UserType::class,
            'locked_at' => 'datetime',
            'mfa_enabled' => 'boolean',
            'mfa_secret' => 'encrypted',
            'preferences' => 'array',
            'failed_login_attempts' => 'integer',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() !== 'admin') {
            return false;
        }

        return $this->hasAnyRole([
            'super-admin',
            'administrator',
            'staff',
        ]);
    }

    public function isLocked(): bool
    {
        return $this->locked_at !== null;
    }

    public function isClient(): bool
    {
        return $this->type === UserType::Client;
    }

    public function isStaffOrAdmin(): bool
    {
        return in_array($this->type, [UserType::Staff, UserType::Administrator], true);
    }

    public function trustedDevices(): HasMany
    {
        return $this->hasMany(TrustedDevice::class);
    }
}
