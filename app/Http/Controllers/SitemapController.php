<?php

namespace App\Http\Controllers;

use App\Domains\Seo\Services\SitemapService;
use Illuminate\Http\Response;

final class SitemapController extends Controller
{
    public function __invoke(SitemapService $sitemap): Response
    {
        return response($sitemap->xml(), 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
