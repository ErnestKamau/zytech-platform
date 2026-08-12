<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

final class RobotsController extends Controller
{
    public function __invoke(): Response
    {
        $sitemap = url('/sitemap.xml');
        $body = implode("\n", [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /portal',
            'Disallow: /account',
            'Disallow: /horizon',
            'Disallow: /telescope',
            'Disallow: /pulse',
            '',
            'Sitemap: '.$sitemap,
            '',
        ]);

        return response($body, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
