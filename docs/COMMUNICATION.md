# Communication Hub

> Phase 13 reference — centralized notifications with Resend email delivery.

## Purpose

Business domains dispatch events. The Communication Hub owns delivery across email (Resend), database notifications, broadcast stubs, activity feed entries, and delivery logs.

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

## Admin

Filament **Communication** group:

- Email templates
- Announcements

## Logs

Every channel attempt writes `notification_logs` (`sent`, `failed`, or `skipped` by preference).

## Wired callers

- Welcome email (registration)
- Quotation submitted / sent
- Portal message / support / meeting notices
