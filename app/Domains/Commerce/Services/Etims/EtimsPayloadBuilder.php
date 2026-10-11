<?php

namespace App\Domains\Commerce\Services\Etims;

use App\Models\Invoice;
use Carbon\Carbon;
use InvalidArgumentException;

/**
 * KRA VSDC payload/signing helpers. Pure functions ported from the KRA TIS
 * spec (OSCU/VSCU v2.0) — not business logic, so these should stay
 * dependency-free and easy to diff against the reference implementation.
 */
final class EtimsPayloadBuilder
{
    /**
     * A=Exempt, B=16% VAT, C=Zero-rated, D=Non-VAT/0%. When the quotation's
     * TaxRate carries an explicit kra_tax_type_code, that always wins (0%
     * is ambiguous between Exempt and Zero-rated and can't be inferred from
     * the rate alone). Otherwise fall back to inferring from the rate
     * itself, for quotations with no TaxRate linked.
     */
    public static function taxTypeCode(float $taxRate, ?string $kraTaxTypeCodeOverride = null): string
    {
        if (filled($kraTaxTypeCodeOverride)) {
            return strtoupper($kraTaxTypeCodeOverride);
        }

        if (abs($taxRate - 16.0) < 0.01) {
            return 'B';
        }

        if (abs($taxRate - 0.0) < 0.01) {
            return 'D';
        }

        throw new InvalidArgumentException("Tax rate {$taxRate}% has no KRA tax type mapping.");
    }

    /**
     * @return array{prc: float, splyAmt: float, totAmt: float}
     */
    public static function vsdcLineAmounts(float $unitPriceIncTax, float $qty): array
    {
        $prc = round($unitPriceIncTax, 2);
        $splyAmt = round($prc * $qty, 2);

        return ['prc' => $prc, 'splyAmt' => $splyAmt, 'totAmt' => $splyAmt];
    }

    /**
     * KRA PINs are exactly 11 characters (letter + 9 digits + letter).
     * Fall back to the generic placeholder rather than send invalid data.
     */
    public static function normalizeCustPin(?string $pin): string
    {
        $pin = strtoupper(trim((string) $pin));

        return preg_match('/^[A-Z][0-9]{9}[A-Z]$/', $pin) === 1 ? $pin : 'P000000000Z';
    }

    public static function truncateVsdcName(?string $value, int $maxLength = 20): string
    {
        $value = trim((string) $value);

        return $value === '' ? '' : mb_substr($value, 0, $maxLength);
    }

    /**
     * @return array<string, mixed>
     */
    public static function parsePayload(?string $json): array
    {
        if (blank($json)) {
            return [];
        }

        $data = json_decode($json, true);

        return is_array($data) ? $data : [];
    }

    /**
     * Insert a dash after every 4th character — how KRA's spec (TIS for
     * OSCU/VSCU v2.0, §6.23.6/6.23.7) says internal data and receipt
     * signature are printed on the receipt.
     */
    public static function dashEvery4(?string $value): string
    {
        return implode('-', str_split((string) $value, 4));
    }

    /**
     * SCU info for a synced invoice: SDC ID, receipt numbers, internal
     * data/signature (raw + dash-formatted), and the QR payload string
     * date#time#sdcId#rcptNo#internalData#receiptSignature.
     *
     * @return array<string, mixed>|null null if the invoice hasn't synced yet
     */
    public static function scuInfo(Invoice $invoice): ?array
    {
        $response = self::parsePayload($invoice->kra_response_payload);
        $data = $response['data'] ?? [];

        if (! is_array($data) || $data === []) {
            return null;
        }

        $sdcId = (string) ($data['sdcId'] ?? '');
        $rcptNo = (string) ($invoice->kra_rcpt_no ?: ($data['rcptNo'] ?? ''));
        $intrlData = (string) ($data['intrlData'] ?? '');
        $rcptSign = (string) ($data['rcptSign'] ?? '');

        $pbctDate = (string) ($data['vsdcRcptPbctDate'] ?? '');
        $date = '';
        $time = '';

        if ($pbctDate !== '') {
            try {
                $parsed = Carbon::createFromFormat('dmyHis', $pbctDate);
                $date = $parsed->format('dmY');
                $time = $parsed->format('His');
            } catch (\Exception) {
                // leave blank if the date can't be parsed
            }
        }

        return [
            'sdc_id' => $sdcId,
            'rcpt_no' => $rcptNo,
            'internal_data' => $intrlData,
            'internal_data_formatted' => self::dashEvery4($intrlData),
            'receipt_signature' => $rcptSign,
            'receipt_signature_formatted' => self::dashEvery4($rcptSign),
            'qr_data' => implode('#', [$date, $time, $sdcId, $rcptNo, $intrlData, $rcptSign]),
        ];
    }
}
