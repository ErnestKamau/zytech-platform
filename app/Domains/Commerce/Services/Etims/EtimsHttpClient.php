<?php

namespace App\Domains\Commerce\Services\Etims;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

final class EtimsHttpClient
{
    /**
     * Send a JSON POST request to the eTIMS (injonge) middleware and return the decoded response.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function postJson(string $endpoint, array $payload, ?string $baseUrlOverride = null): array
    {
        $url = $this->buildUrl($endpoint, $baseUrlOverride);

        Log::info('etims.request', ['url' => $url, 'payload' => $payload]);

        $response = Http::timeout(30)
            ->acceptJson()
            ->asJson()
            ->post($url, $payload);

        if ($response->failed()) {
            Log::error('etims.http_error', ['url' => $url, 'status' => $response->status(), 'body' => $response->body()]);

            throw new RuntimeException("eTIMS API call to {$url} failed with HTTP {$response->status()}.");
        }

        $decoded = $response->json();

        if (! is_array($decoded)) {
            Log::error('etims.invalid_json_response', ['url' => $url, 'body' => $response->body()]);

            throw new RuntimeException("eTIMS API call to {$url} returned a non-JSON response.");
        }

        Log::info('etims.response', ['url' => $url, 'response' => $decoded]);

        return $decoded;
    }

    private function buildUrl(string $endpoint, ?string $baseUrlOverride): string
    {
        if (str_starts_with($endpoint, 'http://') || str_starts_with($endpoint, 'https://')) {
            return $endpoint;
        }

        $base = rtrim((string) ($baseUrlOverride ?: config('etims.base_url', '')), '/');

        if ($base === '') {
            throw new RuntimeException('eTIMS base_url is not configured. Set ETIMS_BASE_URL or a base URL override in eTIMS Settings.');
        }

        return $base.'/'.ltrim($endpoint, '/');
    }
}
