<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

/**
 * Service untuk status & pembuatan aset gambar (stiker / frame) via AI.
 */
class AiService
{
    public function status(): array
    {
        $providers = [
            'openai' => !empty(env('OPENAI_API_KEY')),
            'gemini' => !empty(env('GEMINI_API_KEY')),
            'openrouter' => !empty(env('OPENROUTER_API_KEY')),
        ];

        return [
            'providers' => $providers,
            'default' => (string) env('IMAGE_PROVIDER', 'openai'),
        ];
    }

    public function generate(array $data): array
    {
        $provider = strtolower((string) ($data['provider'] ?: env('IMAGE_PROVIDER', 'openai')));
        $prompt = (string) ($data['prompt'] ?? '');
        $kind = (string) ($data['kind'] ?? 'sticker');
        $transparent = (bool) ($data['transparent'] ?? true);

        if ($provider === 'openai') {
            $apiKey = (string) env('OPENAI_API_KEY');
            if ($apiKey === '') {
                throw new \RuntimeException('OPENAI_API_KEY belum diatur di backend/.env.');
            }
            $model = (string) env('IMAGE_MODEL', 'gpt-image-1');
            $body = [
                'model' => $model,
                'prompt' => $prompt,
                'n' => 1,
                'size' => '1024x1024',
            ];

            $tmpOut = storage_path('app/private/ai-' . uniqid() . '.json');
            $cmd = 'curl -sS -X POST "https://api.openai.com/v1/images/generations" '
                . '-H "Authorization: Bearer ' . escapeshellarg($apiKey) . '" '
                . '-H "Content-Type: application/json" '
                . '-d ' . escapeshellarg(json_encode($body)) . ' '
                . 'https://api.openai.com/v1/images/generations > ' . escapeshellarg($tmpOut);
            exec($cmd, $output, $code);
            $json = json_decode((string) file_get_contents($tmpOut), true);
            @unlink($tmpOut);

            if ($code !== 0 || empty($json['data'][0]['url'])) {
                $message = is_array($json) && !empty($json['error']['message']) ? $json['error']['message'] : 'AI image generation gagal.';
                throw new \RuntimeException('AI image generation gagal: ' . $message);
            }

            $imageUrl = (string) ($json['data'][0]['url'] ?? '');
            $assetPath = 'assets/' . $kind . '/' . uniqid('autoclip_') . '.png';
            $assetFull = storage_path('app/public/' . $assetPath);
            @mkdir(dirname($assetFull), 0777);
            $downloadUrl = rtrim((string) env('APP_URL', 'http://localhost'), '/') . $imageUrl;
            exec('curl -sS ' . escapeshellarg($downloadUrl) . ' -o ' . escapeshellarg($assetFull));
            if (!is_file($assetFull)) {
                throw new \RuntimeException('Gagal menyimpan file hasil generate AI.');
            }

            return [
                'image_url' => '/storage/' . $assetPath,
                'provider' => $provider,
                'prompt' => $prompt,
            ];
        }

        throw new \RuntimeException('Provider AI "' . $provider . '" belum didukung.');
    }
}
