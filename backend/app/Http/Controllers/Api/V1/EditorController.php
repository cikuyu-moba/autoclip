<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;

use App\Models\ApiJob;
use App\Services\ClipService;
use Illuminate\Http\Request;

class EditorController extends Controller
{
    public function __construct(protected ClipService $clipService)
    {
    }

    public function apply(Request $request)
    {
        $data = $request->validate([
            'video_url' => ['required', 'string'],
            'captions' => ['nullable', 'array'],
            'style' => ['nullable', 'array'],
            'stickers' => ['nullable', 'array'],
            'frames' => ['nullable', 'array'],
            'variation' => ['nullable', 'array'],
            'resolution' => ['nullable', 'string'],
            'aspect_ratio' => ['nullable', 'string'],
            'video_bitrate' => ['nullable', 'string'],
            'audio_bitrate' => ['nullable', 'string'],
        ]);

        $job = ApiJob::create([
            'type' => 'edit',
            'status' => 'processing',
            'progress' => 0,
            'message' => 'Mengerjakan render edit...',
            'data' => $data,
        ]);

        try {
            $result = app(ClipService::class)->run('edit', $job, $data);
            $job->update(['status' => 'completed', 'progress' => 100, 'message' => 'Render edit selesai', 'result' => $result]);
            return response()->json($job->fresh()->toApiResponse());
        } catch (\Throwable $e) {
            $job->update(['status' => 'failed', 'error' => $e->getMessage()]);
            return response()->json($job->fresh()->toApiResponse());
        }
    }
}
