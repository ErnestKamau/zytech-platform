# Authentication & Identity

> Phase 2 reference for the Zytech Contractors Platform.

## Purpose

Secure identity for **Administrators**, **Staff**, and **Clients**.

- Public / portal auth → Livewire (`/login`, `/register`, account pages)
- Admin auth → Filament (`/admin/login`)
- Future API tokens → Laravel Sanctum (`HasApiTokens`)

## Domains

| Domain | Owns |
|--------|------|
| `App\Domains\Authentication` | Login, register, password reset, email verification, sessions, lock/unlock, MFA foundation |
| `App\Domains\User` | Profile updates, role/permission assignment, permission cache |

Canonical Eloquent models remain in `App\Models` (`User`, `Role`, `Permission`, `Session`, `TrustedDevice`).

## Roles

Seeded by `RolePermissionSeeder`:

- `super-admin`
- `administrator`
- `staff`
- `client`

Default local users (`AdminUserSeeder`, password `password`):

- `admin@zytech.local` → super-admin + administrator
- `staff@zytech.local` → staff
- `client@zytech.local` → client

## Key flows

```text
RegisterUser → RegistrationService → UserRegistered → WelcomeNotification
AuthenticateUser → AuthenticationService → UserLoggedIn → LogLogin + broadcast listener
AssignRole → RoleService → clears Redis permission cache → RoleAssigned
```

## Security

- Password hashing via Eloquent `hashed` cast
- Email verification (`MustVerifyEmail`)
- Rate limiting + account lock after 5 failed attempts
- Policies for User / Role / Permission
- Filament panel gated by roles (`super-admin`, `administrator`, `staff`)
- MFA preference + `trusted_devices` table (foundation only)

## Redis

`PermissionService` caches permission names via `ApplicationCache`:

- `user.{id}.permissions`
- `roles.all` / `permissions.all`

Invalidate on role/permission assignment.

## Sessions UI note

The account **Sessions** page reads from the `sessions` database table. For that UI to list devices, set `SESSION_DRIVER=database` (Redis sessions still authenticate correctly; they simply will not appear in the table UI).

## Routes

See `routes/auth.php`. Guest: login, register, forgot/reset password. Auth: verify email, account profile/security/sessions/settings.

## Filament

Identity group resources: Users, Roles, Permissions, Sessions.
