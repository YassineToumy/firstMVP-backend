<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ImageProxyController extends Controller
{
    /**
     * Allowed image CDN domains — only proxy from trusted sources.
     */
    private array $allowedDomains = [
        'static.shared.propertyfinder.com',
        'images.weserv.nl',        // mktlist uses this
        'ddfcdn.realtor.ca',       // mktlist
        'mubawab-media.com',       // mubawab
        'www.mubawab.tn',
        'bienici.com',
        'photos.bienici.com',
    ];

    /**
     * Referer headers per domain — needed to bypass hotlink protection.
     */
    private array $refererMap = [
        'propertyfinder.com' => 'https://www.propertyfinder.eg/',
        'mubawab'            => 'https://www.mubawab.tn/',
        'bienici'            => 'https://www.bienici.com/',
        'realtor.ca'         => 'https://www.mktlist.ca/',
        'weserv.nl'          => 'https://www.mktlist.ca/',
    ];

    /**
     * GET /api/v1/image-proxy?url=https://static.shared.propertyfinder.com/...
     */
    public function proxy(Request $request)
    {
        $url = $request->query('url');

        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            return response()->json(['error' => 'Invalid URL'], 400);
        }

        // Security: only proxy from allowed domains
        $host = parse_url($url, PHP_URL_HOST);
        $allowed = false;
        foreach ($this->allowedDomains as $domain) {
            if (str_contains($host, $domain)) {
                $allowed = true;
                break;
            }
        }

        if (!$allowed) {
            return response()->json(['error' => 'Domain not allowed'], 403);
        }

        // Cache key based on URL hash (cache for 24h)
        $cacheKey = 'img_proxy_' . md5($url);

        $cached = Cache::get($cacheKey);
        if ($cached) {
            return response($cached['body'])
                ->header('Content-Type', $cached['content_type'])
                ->header('Cache-Control', 'public, max-age=86400')
                ->header('X-Cache', 'HIT');
        }

        // Determine the correct Referer header
        $referer = 'https://www.google.com/';
        foreach ($this->refererMap as $key => $ref) {
            if (str_contains($url, $key)) {
                $referer = $ref;
                break;
            }
        }

        try {
            $response = Http::withHeaders([
                'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
                'Referer'         => $referer,
                'Accept'          => 'image/avif,image/webp,image/apng,image/svg+xml,image/*,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.9',
                'Sec-Fetch-Dest'  => 'image',
                'Sec-Fetch-Mode'  => 'no-cors',
                'Sec-Fetch-Site'  => 'cross-site',
            ])
            ->timeout(15)
            ->get($url);

            if (!$response->successful()) {
                return response()->json(['error' => 'Upstream error', 'status' => $response->status()], 502);
            }

            $contentType = $response->header('Content-Type') ?? 'image/jpeg';
            $body = $response->body();

            // Cache for 24 hours
            Cache::put($cacheKey, [
                'body'         => $body,
                'content_type' => $contentType,
            ], 86400);

            return response($body)
                ->header('Content-Type', $contentType)
                ->header('Cache-Control', 'public, max-age=86400')
                ->header('X-Cache', 'MISS');

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch image'], 502);
        }
    }
}