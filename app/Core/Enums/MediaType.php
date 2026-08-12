<?php

namespace App\Core\Enums;

enum MediaType: string
{
    case Image = 'image';
    case Video = 'video';
    case Document = 'document';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Image => 'Image',
            self::Video => 'Video',
            self::Document => 'Document',
            self::Other => 'Other',
        };
    }

    public static function fromMime(?string $mime): self
    {
        $mime = strtolower((string) $mime);

        return match (true) {
            str_starts_with($mime, 'image/') => self::Image,
            str_starts_with($mime, 'video/') => self::Video,
            in_array($mime, [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ], true) => self::Document,
            default => self::Other,
        };
    }
}
