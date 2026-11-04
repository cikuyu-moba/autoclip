<?php

namespace App\Services;

/**
 * Transkripsi audio video via OpenAI Whisper API.
 */
class CaptionService
{
    public function transcribe(string $videoPath, ?string $language = null): array
    {
        $apiKey = (string) env('OPENAI_API_KEY');
        if ($apiKey === '') {
            throw new \RuntimeException('OPENAI_API_KEY belum diatur di backend/.env.');
        }

        $cmd = 'curl -sS -X POST "https://api.openai.com/v1/audio/transcriptions" ';
        $cmd .= '-H "Authorization: Bearer ' . escapeshellarg($apiKey) . '" ';
        $cmd .= '-F "model=whisper-1" ';
        $cmd .= '-F "response_format=verbose_json" ';
        if ($language) {
            $cmd .= '-F "language=' . escapeshellarg($language) . '" ';
        }
        $cmd .= '-F "file=@' . escapeshellarg($videoPath);

        $output = shell_exec($cmd);
        $data = json_decode((string) $output, true);

        if (!is_array($data) || empty($data['segments'])) {
            $message = is_array($data) && !empty($data['error']) ? $data['error'] : 'Transkripsi gagal.';
            throw new \RuntimeException('Transkripsi gagal: ' . $message);
        }

        $segments = [];
        foreach ($data['segments'] as $seg) {
            $text = trim((string) ($seg['text'] ?? ''));
            if ($text === '') continue;
            $start = (float) ($seg['start'] ?? 0);
            $end = (float) ($seg['end'] ?? ((float) ($seg['duration'] ?? 0) + $start));
            $words = [];
            if (!empty($seg['words'])) {
                foreach ($seg['words'] as $w) {
                    $words[] = [
                        'word' => $w['word'] ?? '',
                        'start' => (float) ($w['start'] ?? $start),
                        'end' => (float) ($w['end'] ?? $end),
                    ];
                }
            }
            $segments[] = [
                'id' => count($segments) + 1,
                'start' => $start,
                'end' => $end,
                'duration' => $end - $start,
                'text' => $text,
                'words' => $words,
            ];
        }

        return ['segments' => $segments];
    }
}
