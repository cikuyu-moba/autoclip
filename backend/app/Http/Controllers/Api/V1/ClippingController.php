<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;

use App\Models\ApiJob;
use App\Services\ClipService;
use Illuminate\Http\Request;

class ClippingController extends Controller
{
    public function __construct(protected ClipService $clipService)
    {
    }

    public function preview(Request $request)
    {
        $data = $request->validate([
            'url' => ['required_without:video_url', 'nullable', 'string'],
            'video_url' => ['nullable', 'string'],
            'start_time' => ['nullable', 'numeric'],
            'end_time' => ['nullable', 'numeric'],
        ]);

        $url = (string) ($data['url'] ?? $data['video_url'] ?? '');
        if ($url === '') {
            return response()->json(['detail' => 'URL source video wajib diisi.'], 422);
        }

        $job = ApiJob::create([
            'type' => 'preview',
            'status' => 'processing',
            'progress' => 0,
            'message' => 'Menyiapkan source video...',
            'data' => $data,
        ]);

        try {
            $result = app(ClipService::class)->run('preview', $job, $data);
            $job->update(['status' => 'completed', 'progress' => 100, 'message' => 'Preview selesai', 'result' => $result]);
            return response()->json($job->fresh()->toApiResponse());
        } catch (\Throwable $e) {
            $job->update(['status' => 'failed', 'error' => $e->getMessage()]);
            return response()->json($job->fresh()->toApiResponse());
        }
    }

    public function clip(Request $request)
    {
        $data = $request->validate([
            'url' => ['required', 'string'],
            'start_time' => ['required', 'numeric'],
            'end_time' => ['required', 'numeric'],
            'resolution' => ['nullable', 'string'],
            'aspect_ratio' => ['nullable', 'string'],
            'video_bitrate' => ['nullable', 'string'],
            'audio_bitrate' => ['nullable', 'string'],
        ]);

        $job = ApiJob::create([
            'type' => 'clip',
            'status' => 'processing',
            'progress' => 0,
            'message' => 'Menyiapkan source video...',
            'data' => $data,
        ]);

        try {
            $result = app(ClipService::class)->run('clip', $job, $data);
            $job->update(['status' => 'completed', 'progress' => 100, 'message' => 'Potongan video selesai', 'result' => $result]);
            return response()->json($job->fresh()->toApiResponse());
        } catch (\Throwable $e) {
            $job->update(['status' => 'failed', 'error' => $e->getMessage()]);
            return response()->json($job->fresh()->toApiResponse());
        }
    }

    public function trim(Request $request)
    {
        $data = $request->validate([
            'video_url' => ['required', 'string'],
            'start_time' => ['required', 'numeric'],
            'end_time' => ['required', 'numeric'],
            'resolution' => ['nullable', 'string'],
            'aspect_ratio' => ['nullable', 'string'],
            'video_bitrate' => ['nullable', 'string'],
            'audio_bitrate' => ['nullable', 'string'],
        ]);

        $job = ApiJob::create([
            'type' => 'trim',
            'status' => 'processing',
            'progress' => 0,
            'message' => 'Memotong video dari source...',
            'data' => $data,
        ]);

        try {
            $result = app(ClipService::class)->run('trim', $job, $data);
            $job->update(['status' => 'completed', 'progress' => 100, 'message' => 'Potongan video selesai', 'result' => $result]);
            return response()->json($job->fresh()->toApiResponse());
        } catch (\Throwable $e) {
            $job->update(['status' => 'failed', 'error' => $e->getMessage()]);
            return response()->json($job->fresh()->toApiResponse());
        }
    }

    public function multiClip(Request $request)
    {
        $data = $request->validate([
            'segments' => ['required', 'array'],
            'url' => ['required_without:video_url', 'nullable', 'string'],
            'video_url' => ['nullable', 'string'],
            'resolution' => ['nullable', 'string'],
            'aspect_ratio' => ['nullable', 'string'],
            'video_bitrate' => ['nullable', 'string'],
            'audio_bitrate' => ['nullable', 'string'],
        ]);

        if (empty($data['segments'])) {
            return response()->json(['detail' => 'Segmen wajib diisi.'], 422);
        }

        $job = ApiJob::create([
            'type' => 'multi_clip',
            'status' => 'processing',
            'progress' => 0,
            'message' => 'Menyiapkan source video...',
            'data' => $data,
        ]);

        try {
            $result = app(ClipService::class)->run('multi_clip', $job, $data);
            $job->update(['status' => 'completed', 'progress' => 100, 'message' => 'Rangkuman multi-clip selesai', 'result' => $result]);
            return response()->json($job->fresh()->toApiResponse());
        } catch (\Throwable $e) {
            $job->update(['status' => 'failed', 'error' => $e->getMessage()]);
            return response()->json($job->fresh()->toApiResponse());
        }
    }
}
