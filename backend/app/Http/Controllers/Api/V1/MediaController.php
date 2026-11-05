<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;

use App\Services\MediaService;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function __construct(protected MediaService $media)
    {
    }

    public function info(Request $request)
    {
        $validated = $request->validate([
            'url' => ['required_without:video_url', 'nullable', 'string'],
            'video_url' => ['nullable', 'string'],
        ]);

        $url = (string) ($validated['url'] ?? $validated['video_url'] ?? '');
        if ($url === '') {
            return response()->json(['detail' => 'URL video wajib diisi.'], 422);
        }

        return response()->json(app(MediaService::class)->info($url));
    }

    public function upload(Request $request)
    {
        return response()->json(app(MediaService::class)->upload($request));
    }

    public function library(Request $request)
    {
        $limit = (int) $request->query('limit', 60);

        return response()->json(['items' => app(MediaService::class)->library($limit)]);
    }

    public function assets(Request $request)
    {
        $kind = (string) $request->query('kind', 'sticker');
        $limit = (int) $request->query('limit', 120);

        return response()->json(['items' => app(MediaService::class)->assets($kind, $limit)]);
    }
}
