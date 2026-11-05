<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;

use App\Services\AiService;
use Illuminate\Http\Request;

class AiController extends Controller
{
    public function __construct(protected AiService $aiService)
    {
    }

    public function status()
    {
        return response()->json(app(AiService::class)->status());
    }

    public function image(Request $request)
    {
        $data = $request->validate([
            'prompt' => ['required', 'string'],
            'kind' => ['nullable', 'string', 'default:sticker'],
            'provider' => ['nullable', 'string'],
            'transparent' => ['nullable', 'boolean'],
        ]);

        $result = app(AiService::class)->generate($data);

        return response()->json($result);
    }
}
