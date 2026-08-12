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
| `App\Domains\Authentication` | Login, register, password reset, email verification, sessions, lock/unlock, Email/SMS OTP 2FA |
| `App\Domains\User` | Profile updates, role/permission assignment, permission cache |
| `App\Domains\Communication` | Resend mail + Twilio SMS helpers used by OTP |

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
RegisterUser → RegistrationService → redirect verification.notice
AuthenticateUser → AuthenticationService
  → RequiresEmailVerification | RequiresTwoFactor | Authenticated
TwoFactorChallenge → Email OTP (Resend/mail) or SMS OTP (Twilio)
```

### Portal sign-in

1. Password at `/login`
2. If email unverified → `/email/verify` — **choose Email or SMS**, enter OTP → marks verified **and enables login 2FA on that channel**
3. If `mfa_enabled` with at least one enrolled channel → logout + pending session → `/login/two-factor`
4. User chooses **Email** or **SMS**, receives 6-digit code (10 min TTL), verifies → full login → portal

Enrollment also available later at `/account/security` (add/change methods, or disable).

Auth OTP emails use synchronous `AuthOtpMail` with branded Blade bodies under `resources/views/emails/auth/` (not the `mail` queue). Other app mail still needs a worker on the `mail` queue.

## Security

- Password hashing via Eloquent `hashed` cast
- Email verification (`MustVerifyEmail`); portal routes use `verified` middleware
- Rate limiting + account lock after 5 failed attempts
- OTP: Redis/cache hashed codes, resend throttle 60s, max 5 verify attempts
- SMS requires Twilio (`TWILIO_SID`, `TWILIO_AUTH_TOKEN`, `TWILIO_FROM`) — no log fallback
- Policies for User / Role / Permission
- Filament panel gated by roles (`super-admin`, `administrator`, `staff`)
- Preferences JSON: `mfa_email_enabled`, `mfa_sms_enabled`; phone must be E.164 for SMS

## Env

```env
MAIL_MAILER=resend
MAIL_FROM_ADDRESS=noreply@your-verified-domain.com
RESEND_API_KEY=
TWILIO_SID=
TWILIO_AUTH_TOKEN=
TWILIO_FROM=
```

`MAIL_FROM_ADDRESS` must be on a domain verified in the Resend dashboard. Addresses on `example.com` are rejected.
## Redis

`PermissionService` caches permission names via `ApplicationCache`:

- `user.{id}.permissions`
- `roles.all` / `permissions.all`

Invalidate on role/permission assignment.

OTP keys: `auth.otp.{purpose}.{userId}.{channel}`.

## Sessions UI note

The account **Sessions** page reads from the `sessions` database table. For that UI to list devices, set `SESSION_DRIVER=database` (Redis sessions still authenticate correctly; they simply will not appear in the table UI).

## Routes

See `routes/auth.php`. Guest: login, two-factor challenge, register, forgot/reset password. Auth: verify email, account profile/security/sessions/settings.

Portal: `auth` + `verified` + `EnsurePortalAccess`.

## Filament

Identity group resources: Users, Roles, Permissions, Sessions. Admin panel login does not use portal OTP 2FA.
