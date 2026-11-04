<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\MediaController;
use App\Http\Controllers\Api\V1\JobController;
use App\Http\Controllers\Api\V1\ClippingController;
use App\Http\Controllers\Api\V1\EditorController;
use App\Http\Controllers\Api\V1\ExportController;
use App\Http\Controllers\Api\V1\CaptionController;
use App\Http\Controllers\Api\V1\AiController;

/*
|--------------------------------------------------------------------------
| Autoclip API Routes (v1)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Media: info, upload, library, assets
    Route::get('/media/info', [MediaController::class, 'info'])->name('api.v1.media.info');
    Route::post('/media/upload', [MediaController::class, 'upload'])->name('api.v1.media.upload');
    Route::get('/media/library', [MediaController::class, 'library'])->name('api.v1.media.library');
    Route::get('/media/assets', [MediaController::class, 'assets'])->name('api.v1.media.assets');

    // Jobs
    Route::get('/jobs/{id}', [JobController::class, 'show'])->name('api.v1.jobs.show');

    // Clipping & processing
    Route::post('/clipping/preview', [ClippingController::class, 'preview'])->name('api.v1.clipping.preview');
    Route::post('/clipping/clip', [ClippingController::class, 'clip'])->name('api.v1.clipping.clip');
    Route::post('/clipping/multi-clip', [ClippingController::class, 'multiClip'])->name('api.v1.clipping.multi-clip');
    Route::post('/clipping/trim', [ClippingController::class, 'trim'])->name('api.v1.clipping.trim');

    // Editor & Export
    Route::post('/editor/apply', [EditorController::class, 'apply'])->name('api.v1.editor.apply');
    Route::post('/export/render', [ExportController::class, 'render'])->name('api.v1.export.render');

    // Caption (Whisper)
    Route::post('/caption/extract', [CaptionController::class, 'extract'])->name('api.v1.caption.extract');

    // AI
    Route::get('/ai/status', [AiController::class, 'status'])->name('api.v1.ai.status');
    Route::post('/ai/image', [AiController::class, 'image'])->name('api.v1.ai.image');
});
