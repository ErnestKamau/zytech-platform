<?php

namespace App\Domains\Communication\Support;

final class PhoneNumber
{
    /**
     * Normalize a Kenyan phone number into E.164 (+254XXXXXXXXX).
     * Accepts 07xx/01xx local format, 254xx, or +254xx. Returns null if unparseable.
     */
    public static function toE164Kenyan(?string $raw): ?string
    {
        if ($raw === null || trim($raw) === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $raw) ?? '';

        if (str_starts_with($digits, '254') && strlen($digits) === 12) {
            $local = substr($digits, 3);
        } elseif (str_starts_with($digits, '0') && strlen($digits) === 10) {
            $local = substr($digits, 1);
        } elseif (strlen($digits) === 9) {
            $local = $digits;
        } else {
            return null;
        }

        if (! in_array($local[0], ['7', '1'], true)) {
            return null;
        }

        return '+254'.$local;
    }
}
