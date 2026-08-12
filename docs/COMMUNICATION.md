# Communication Hub

> Phase 13 reference — centralized notifications with Resend email + Reverb realtime.

## Purpose

Business domains dispatch events. The Communication Hub owns delivery across email (Resend), database notifications, Reverb broadcast, activity feed entries, and delivery logs.

## Resend setup

1. Create an API key at [resend.com](https://resend.com/api-keys)
2. Set in `.env`:

```env
MAIL_MAILER=resend
RESEND_API_KEY=re_xxxxxxxx
MAIL_FROM_ADDRESS=noreply@your-verified-domain.com
```

Local without a key: set `MAIL_MAILER=log` so mail is written to the log instead of Resend.

Package: `resend/resend-php` (Laravel `resend` mail transport).

## Domain

`App\Domains\Communication`

- `CommunicationService::notify()` — pipeline entry point
- `TemplateService` — Redis-cached templates with `{{placeholders}}`
- `AnnouncementService` — platform announcements (website + portal flags)
- `ActivityFeedService` — chronological feed rows
- `Events\NotificationPushed` — Reverb payload for UI toasts

## Reverb realtime

When the Broadcast channel runs, `CommunicationService` dispatches `NotificationPushed`:

- Private channel `App.Models.User.{id}` when a user is targeted
- Public channel `platform.announcements` for platform-wide pushes

Frontend (`resources/js/app.js`):

- Echo listens for `.NotificationPushed`
- Dispatches `zy-toast` window events
- Alpine `zyToasts()` renders into `#zy-toast-host` (website + portal layouts)
- Requires `<meta name="user-id">` for private channel auth (`/broadcasting/auth` with `web` + `auth`)

Run Reverb locally via `composer run dev` (or `php artisan reverb:start`) with `BROADCAST_CONNECTION=reverb` and matching `VITE_REVERB_*` vars.

## Admin

Filament **Communication** group:

- Email templates
- Announcements

## Logs

Every channel attempt writes `notification_logs` (`sent`, `failed`, or `skipped` by preference).

## Email bodies

Transactional mail uses the shared Horizon shell:

- Layout: `resources/views/emails/layouts/horizon.blade.php` (Zytech sage/stone)
- Partials: `emails/partials/{copy,otp,alert,panel,cta}`
- Auth OTP: `emails/auth/{verification,login,enrollment}-code.blade.php` via sync `AuthOtpMail`
- Hub mail: `emails/message.blade.php` via queued `TemplatedMail` + Filament template text

## Wired callers

- Welcome email (registration)
- Quotation submitted / sent
- Portal message / support / meeting notices

## Future channels (partial)

- **SMS OTP for portal login:** Twilio wired via `TwilioSmsService` + `NotificationChannel::Sms`
- Roadmap Phase 16–18: broader Twilio SMS marketing/notices, WhatsApp Business, browser Web Push — same hub pipeline where applicable
