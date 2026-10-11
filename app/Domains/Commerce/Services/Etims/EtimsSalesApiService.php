<?php

namespace App\Domains\Commerce\Services\Etims;

use App\Core\Enums\EtimsSyncStatus;
use App\Domains\Company\Services\CompanyService;
use App\Models\EtimsSetting;
use App\Models\Invoice;
use Illuminate\Support\Carbon;
use RuntimeException;

final class EtimsSalesApiService
{
    public function __construct(
        private readonly EtimsHttpClient $http,
        private readonly EtimsInvoiceItemResolver $itemResolver,
        private readonly CompanyService $companies,
    ) {}

    /**
     * @return array{
     *     itemList: array<int, array<string, mixed>>,
     *     taxblAmtA: float, taxblAmtB: float, taxblAmtC: float, taxblAmtD: float,
     *     taxAmtA: float, taxAmtB: float, taxAmtC: float, taxAmtD: float
     * }
     */
    public function buildItemList(Invoice $invoice, EtimsSetting $settings): array
    {
        $invoice->loadMissing('items.product');

        $bands = ['A' => 0.0, 'B' => 0.0, 'C' => 0.0, 'D' => 0.0];
        $taxblBands = ['A' => 0.0, 'B' => 0.0, 'C' => 0.0, 'D' => 0.0];
        $itemList = [];
        $invoice->loadMissing('quotation.taxRate');
        $taxRate = (float) $invoice->quotation?->tax_rate ?: 16.0;
        $taxTyCd = EtimsPayloadBuilder::taxTypeCode($taxRate, $invoice->quotation?->taxRate?->kra_tax_type_code);

        foreach ($invoice->items as $index => $item) {
            $qty = (float) ($item->quantity ?: 1);
            $lineTotal = round((float) $item->line_total, 2);
            $taxAmt = $taxTyCd === 'B' ? round($lineTotal * $taxRate / (100 + $taxRate), 2) : 0.0;
            $taxblAmt = round($lineTotal - $taxAmt, 2);

            $bands[$taxTyCd] += $taxAmt;
            $taxblBands[$taxTyCd] += $taxblAmt;

            $codes = $this->itemResolver->resolve($item, $settings);
            $amounts = EtimsPayloadBuilder::vsdcLineAmounts($lineTotal / max($qty, 0.0001), $qty);

            $itemList[] = [
                'itemSeq' => $index + 1,
                'itemCd' => $codes['itemCd'],
                'itemNm' => $item->label,
                'bcd' => '',
                'pkgUnitCd' => 'NT',
                'pkg' => 1,
                'qtyUnitCd' => 'U',
                'itemClsCd' => $codes['itemClsCd'],
                'qty' => round($qty, 2),
                'prc' => $amounts['prc'],
                'splyAmt' => $amounts['splyAmt'],
                'dcRt' => 0.0,
                'dcAmt' => 0.0,
                'taxTyCd' => $taxTyCd,
                'taxAmt' => $taxAmt,
                'taxblAmt' => $taxblAmt,
                'totAmt' => $amounts['totAmt'],
            ];
        }

        if ($itemList === []) {
            throw new RuntimeException('Cannot send an invoice with no line items to eTIMS.');
        }

        return [
            'itemList' => $itemList,
            'taxblAmtA' => round($taxblBands['A'], 2),
            'taxblAmtB' => round($taxblBands['B'], 2),
            'taxblAmtC' => round($taxblBands['C'], 2),
            'taxblAmtD' => round($taxblBands['D'], 2),
            'taxAmtA' => round($bands['A'], 2),
            'taxAmtB' => round($bands['B'], 2),
            'taxAmtC' => round($bands['C'], 2),
            'taxAmtD' => round($bands['D'], 2),
        ];
    }

    /**
     * @param  array{rcptTyCd: string, orgInvcNo: string, refno?: ?string, itemData?: array<string, mixed>}  $options
     * @return array<string, mixed>
     */
    public function buildVsdcPayload(Invoice $invoice, EtimsSetting $settings, array $options): array
    {
        $invoice->loadMissing('client');
        $client = $invoice->client;
        $company = $this->companies->current();

        $custPin = EtimsPayloadBuilder::normalizeCustPin($client?->kra_pin);
        $custNm = $client?->name ?? 'Walk-in Customer';
        $custMblNo = $client?->phone ?? '0700000000';
        $tradeName = EtimsPayloadBuilder::truncateVsdcName($company?->name ?? 'Zytech Contractors');

        $itemData = $options['itemData'] ?? $this->buildItemList($invoice, $settings);

        $now = Carbon::now();
        $cfmDt = $now->format('YmdHis');
        $refno = $options['refno'] ?? $invoice->reference_number;

        return [
            'tin' => $company?->kra_pin,
            'bhfId' => '00',
            'deviceSerial' => $settings->device_serial,
            'invcNo' => $refno,
            'refno' => $refno,
            'orgInvcNo' => $options['orgInvcNo'],
            'custTin' => $custPin,
            'custNm' => $custNm,
            'salesTyCd' => 'N',
            'rcptTyCd' => $options['rcptTyCd'],
            'pmtTyCd' => '01',
            'salesSttsCd' => '02',
            'cfmDt' => $cfmDt,
            'salesDt' => $now->format('Ymd'),
            'stockRlsDt' => $cfmDt,
            'rfdDt' => $options['rfdDt'] ?? null,
            'rfdRsnCd' => $options['rfdRsnCd'] ?? null,
            'totItemCnt' => count($itemData['itemList']),
            'taxblAmtA' => $itemData['taxblAmtA'] ?? 0.0,
            'taxblAmtB' => $itemData['taxblAmtB'],
            'taxblAmtC' => $itemData['taxblAmtC'] ?? 0.0,
            'taxblAmtD' => $itemData['taxblAmtD'],
            'taxRtA' => 0.00,
            'taxRtB' => 16.00,
            'taxRtC' => 0.00,
            'taxRtD' => 0.00,
            'taxAmtA' => $itemData['taxAmtA'] ?? 0.0,
            'taxAmtB' => $itemData['taxAmtB'],
            'taxAmtC' => $itemData['taxAmtC'] ?? 0.0,
            'taxAmtD' => $itemData['taxAmtD'],
            'totTaxblAmt' => round(($itemData['taxblAmtA'] ?? 0.0) + $itemData['taxblAmtB'] + ($itemData['taxblAmtC'] ?? 0.0) + $itemData['taxblAmtD'], 2),
            'totTaxAmt' => round(($itemData['taxAmtA'] ?? 0.0) + $itemData['taxAmtB'] + ($itemData['taxAmtC'] ?? 0.0) + $itemData['taxAmtD'], 2),
            'totAmt' => round((float) $invoice->total_amount, 2),
            'prchrAcptcYn' => 'Y',
            'remark' => $tradeName,
            'regrId' => '1',
            'regrNm' => 'System',
            'modrId' => '1',
            'modrNm' => $tradeName,
            'receipt' => [
                'rptNo' => 0,
                'curRcptNo' => 0,
                'totRcptNo' => 0,
                'custTin' => $custPin,
                'custMblNo' => $custMblNo,
                'rcptPbctDt' => $cfmDt,
                'intrlData' => '',
                'rcptSign' => '',
                'jrnl' => '',
                'trdeNm' => $tradeName,
                'adrs' => $company?->location ?? '',
                'topMsg' => $tradeName,
                'btmMsg' => 'Thank You',
                'prchrAcptcYn' => 'Y',
            ],
            'itemList' => $itemData['itemList'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function sendInvoice(Invoice $invoice, EtimsSetting $settings): array
    {
        $this->assertDeviceConfigured($settings);

        $payload = $this->buildVsdcPayload($invoice, $settings, [
            'rcptTyCd' => 'S',
            'orgInvcNo' => '0',
        ]);

        return $this->postVsdcPayload($invoice, $settings, $payload, 'invoice');
    }

    /**
     * Full-reversal credit note: resends the parent's exact item list
     * (quantities unchanged), marked as a refund against orgInvcNo.
     *
     * @return array<string, mixed>
     */
    public function sendCreditNote(Invoice $creditNote, Invoice $parent, EtimsSetting $settings): array
    {
        $this->assertDeviceConfigured($settings);

        if (blank($parent->kra_invc_no)) {
            throw new RuntimeException('Original invoice has not been synced to eTIMS; cannot issue a credit note.');
        }

        $parentPayload = EtimsPayloadBuilder::parsePayload($parent->kra_request_payload);

        if (blank($parentPayload['itemList'] ?? null)) {
            throw new RuntimeException('Original invoice eTIMS payload is missing its item list.');
        }

        $itemData = [
            'itemList' => $parentPayload['itemList'],
            'taxblAmtA' => $parentPayload['taxblAmtA'] ?? 0,
            'taxblAmtB' => $parentPayload['taxblAmtB'] ?? 0,
            'taxblAmtC' => $parentPayload['taxblAmtC'] ?? 0,
            'taxblAmtD' => $parentPayload['taxblAmtD'] ?? 0,
            'taxAmtA' => $parentPayload['taxAmtA'] ?? 0,
            'taxAmtB' => $parentPayload['taxAmtB'] ?? 0,
            'taxAmtC' => $parentPayload['taxAmtC'] ?? 0,
            'taxAmtD' => $parentPayload['taxAmtD'] ?? 0,
        ];

        $payload = $this->buildVsdcPayload($creditNote, $settings, [
            'rcptTyCd' => 'R',
            'orgInvcNo' => $parent->kra_invc_no,
            'refno' => config('etims.credit_note_refno_prefix', 'CN-').$parent->reference_number,
            'itemData' => $itemData,
            'rfdDt' => Carbon::now()->format('YmdHis'),
            // '06' = Other. No refund-reason-code capture UI yet (matches the
            // reference implementation's limitation — deferred to a later phase).
            'rfdRsnCd' => '06',
        ]);

        return $this->postVsdcPayload($creditNote, $settings, $payload, 'credit note');
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function postVsdcPayload(Invoice $invoice, EtimsSetting $settings, array $payload, string $context): array
    {
        try {
            $response = $this->http->postJson(
                config('etims.urls.sales', 'injongeReceiptsEtims'),
                $payload,
                $settings->base_url_override,
            );
        } catch (\Throwable $e) {
            $invoice->forceFill([
                'kra_sync_status' => EtimsSyncStatus::Failed,
                'kra_last_error' => $e->getMessage(),
            ])->save();

            throw $e;
        }

        $statusCode = $response['status'] ?? $response['resultCd'] ?? null;

        if ($statusCode !== '000') {
            $message = $response['message'] ?? ($response['resultMsg'] ?? 'Unknown eTIMS error');

            $invoice->forceFill([
                'kra_sync_status' => EtimsSyncStatus::Failed,
                'kra_last_error' => "Failed to send {$context} to eTIMS: {$message}",
            ])->save();

            throw new RuntimeException("Failed to send {$context} to eTIMS: {$message}");
        }

        $data = $response['data'] ?? [];

        $invoice->forceFill([
            'kra_request_payload' => json_encode($payload),
            'kra_response_payload' => json_encode($response),
            'kra_rcpt_no' => $data['rcptNo'] ?? null,
            'kra_sdc_id' => $data['sdcId'] ?? null,
            'kra_invc_no' => $data['invcNo'] ?? null,
            'kra_sync_status' => EtimsSyncStatus::Synced,
            'kra_last_error' => null,
            'kra_synced_at' => now(),
        ])->save();

        return $response;
    }

    private function assertDeviceConfigured(EtimsSetting $settings): void
    {
        if (blank($settings->device_serial)) {
            throw new RuntimeException('Device serial is required for eTIMS sync. Configure it in eTIMS Settings.');
        }
    }
}
