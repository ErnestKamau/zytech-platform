<?php

namespace Tests\Feature\Http;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class TrustedProxiesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::get('/__proxy-probe', fn (Request $request) => [
            'ip' => $request->ip(),
            'secure' => $request->secure(),
        ])->middleware('web');
    }

    public function test_cloudflare_forwarded_headers_are_trusted(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '172.64.0.10'])
            ->withHeaders([
                'X-Forwarded-For' => '102.213.49.78',
                'X-Forwarded-Proto' => 'https',
            ])
            ->get('/__proxy-probe')
            ->assertJson(['ip' => '102.213.49.78', 'secure' => true]);
    }

    public function test_forwarded_headers_from_untrusted_sources_are_ignored(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.50'])
            ->withHeaders([
                'X-Forwarded-For' => '1.2.3.4',
                'X-Forwarded-Proto' => 'https',
            ])
            ->get('/__proxy-probe')
            ->assertJson(['ip' => '203.0.113.50', 'secure' => false]);
    }
}
