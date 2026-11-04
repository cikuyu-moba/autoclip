<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\ApiJob;
use App\Services\CaptionService;
use Illuminate\Http\Request;

class CaptionController extends Controller
{
    public function __construct(protected CaptionService $captionService)
    {
    }

    public function extract(Request $request)
    {
        $data = $request->validate([
            'video_url' => ['required', 'string'],
            'language' => ['nullable', 'string'],
        ]);

        $job = ApiJob::create([
            'type' => 'caption',
            'status' => 'processing',
            'progress' => 0,
            'message' => 'Menyiapkan video untuk transkripsi...',
            'data' => $data,
        ]);

        try {
            // video_url pada tahap ini berupa path lokal yang sudah tersedia.
            $videoPath = $this->resolveVideoPath((string) $data['video_url']);
            $result = app(CaptionService::class)->transcribe($videoPath, $data['language'] ?? null);

            $job->update([
                'status' => 'completed',
                'progress' => 100,
                'message' => 'Transkripsi selesai',
                'result' => $result,
            ]);

            return response()->json($job->fresh()->toApiResponse());
        } catch (\Throwable $e) {
            $job->update(['status' => 'failed', 'error' => $e->getMessage()]);
            return response()->json($job->fresh()->toApiResponse());
        }
    }

    protected function resolveVideoPath(string $videoUrl): string
    {
        if (str_starts_with($videoUrl, '/') && !preg_match('#^https?://#i', $videoUrl)) {
            $base = str_starts_with($videoUrl, '/storage')
                ? rtrim(storage_path('app/public'), '/')
                : rtrim(storage_path('app/private'), '/');
            return rtrim($base, '/') . substr($videoUrl, strlen(str_starts_with($videoUrl, '/storage') ? strlen('/storage') : 0));
        }
        return $videoUrl;
    }
}
