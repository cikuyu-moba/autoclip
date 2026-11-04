<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\ApiJob;
use App\Services\ClipService;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function __construct(protected ClipService $clipService)
    {
    }

    public function render(Request $request)
    {
        $data = $request->validate([
            'video_url' => ['required', 'string'],
            'resolution' => ['nullable', 'string'],
            'aspect_ratio' => ['nullable', 'string'],
            'video_bitrate' => ['nullable', 'string'],
            'audio_bitrate' => ['nullable', 'string'],
            'variation' => ['nullable', 'array'],
        ]);

        $job = ApiJob::create([
            'type' => 'export',
            'status' => 'processing',
            'progress' => 0,
            'message' => 'Merender video...',
            'data' => $data,
        ]);

        try {
            $result = app(ClipService::class)->run('export', $job, $data);
            $job->update(['status' => 'completed', 'progress' => 100, 'message' => 'Export video selesai', 'result' => $result]);
            return response()->json($job->fresh()->toApiResponse());
        } catch (\Throwable $e) {
            $job->update(['status' => 'failed', 'error' => $e->getMessage()]);
            return response()->json($job->fresh()->toApiResponse());
        }
    }
}
