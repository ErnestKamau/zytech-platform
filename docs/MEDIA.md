# Media

> Phase 5 reference for the Zytech Contractors Platform.

## Purpose

All new uploads go through the Media domain (Spatie Media Library). Folders, tags, conversions, and usage tracking live here so later domains do not store files on their own.

## Public website assets

The live homepage, about, services, and projects pages keep using the files in `public/media/zyntech` via `config/zyntech-media.php`.

Those files are **not** moved or deleted. The media seeder copies them into the library with `preservingOriginal()` and marks them `protected_site_asset` so Filament cannot delete them.

## Domain

`App\Domains\Media` owns:

- Uploads, deletes, moves, tags
- Image conversions (thumb, small, medium, large, webp, hero)
- Search by name / file name / MIME
- Usage records (which model uses which file)

Canonical models: `App\Models\Media` (Spatie), `MediaFolder`, `MediaTag`, `MediaUsage`.

Documents, certificates, and downloads use the private `local` disk. Everything else uses `public`.

## Admin

Filament **Media** group:

- Library
- Folders
- Tags

Permissions: `media.view` (staff) and `media.manage` (administrators). Super-admins bypass policies.

## Cache

`ApplicationCache` keys (prefix `zytech.`):

- `media.folders.tree`
- `media.counts`
- `media.recent`

## Queue

Conversions run on the `media` Horizon queue. Video transcoding is not enabled yet (FFmpeg is a later phase).

## Seed

`MediaSeeder` creates Website / Images / Videos / Posters folders and imports the current site files without removing `public/media/zyntech`.
