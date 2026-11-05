<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaService
{
    public function info(string $url): array
    {
        if ($this->isLocalPath($url)) {
            $info = $this->ffprobe($url);
            return array_merge(['url' => $url], $info);
        }

        $binary = escapeshellarg((string) config('autoclip.yt_dlp.binary', 'yt-dlp'));

        $tempDir = storage_path('app/private');
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0777, true);
        }
        $errFile = rtrim($tempDir, '/\\') . DIRECTORY_SEPARATOR . 'ytdlp-meta-' . uniqid('', true) . '.log';

        $output = [];
        $code = 0;
        exec(
            $binary . ' --no-playlist --no-warnings -J '
            . escapeshellarg($url) . ' 2>' . escapeshellarg($errFile),
            $output,
            $code
        );

        // stdout harus murni JSON; stderr (warning/error) dibaca terpisah.
        $json = implode("\n", $output);
        $data = json_decode($json, true);

        $detail = '';
        if (is_file($errFile)) {
            $detail = trim((string) @file_get_contents($errFile));
            @unlink($errFile);
        }

        if ($code !== 0 || !is_array($data)) {
            $lines = preg_split('/\R/', $detail) ?: [];
            $detail = trim(implode(' | ', array_slice(array_filter($lines), -3)));

            throw new \RuntimeException(
                'Tidak dapat mengambil metadata video dari URL tersebut.'
                . ($detail !== '' ? ' Detail: ' . $detail : '')
            );
        }

        return [
            'url' => $url,
            'duration' => $data['duration'] ?? null,
            'width' => $data['width'] ?? null,
            'height' => $data['height'] ?? null,
            'thumbnail' => $data['thumbnail'] ?? null,
            'title' => $data['title'] ?? null,
            'uploader' => $data['uploader'] ?? ($data['channel'] ?? null),
        ];
    }

    public function upload(Request $request): array
    {
        $file = $request->file('file');
        if (!$file) {
            throw new \RuntimeException('File video wajib diunggah.');
        }
        if ($file->getSize() > 500 * 1024 * 1024) {
            throw new \RuntimeException('Maksimal ukuran video adalah 500 MB.');
        }

        $path = $file->store('videos', 'public');
        $fullPath = Storage::disk('public')->path($path);

        return [
            'video_url' => '/storage/' . $path,
            'filename' => $file->getClientOriginalName(),
            'duration' => $this->ffprobeDuration($fullPath),
            'width' => $this->ffprobeWidth($fullPath),
            'height' => $this->ffprobeHeight($fullPath),
            'size' => $file->getSize(),
            'kind' => 'upload',
        ];
    }

    public function library(int $limit = 60): array
    {
        $files = Storage::disk('public')->files('videos');
        $items = [];
        foreach ($files as $file) {
            $items[] = [
                'video_url' => '/storage/' . $file,
                'filename' => basename($file),
                'kind' => $this->guessKind($file),
                'size' => (int) (Storage::disk('public')->size($file) ?: 0),
                'duration' => null,
            ];
        }
        // urutkan berdasarkan waktu terakhir diubah
        usort($items, function ($a, $b) {
            return strcmp($b['filename'], $a['filename']);
        });
        return ['items' => array_slice($items, 0, max(1, (int) $limit))];
    }

    public function assets(string $kind, int $limit = 120): array
    {
        $prefix = 'assets/' . $kind;
        $files = Storage::disk('public')->files($prefix);
        $items = [];
        foreach ($files as $file) {
            $items[] = [
                'id' => basename($file),
                'src' => '/storage/' . $file,
                'name' => basename($file),
                'kind' => $kind,
                'size' => (int) (Storage::disk('public')->size($file) ?: 0),
            ];
        }
        return ['items' => array_slice($items, 0, max(1, (int) $limit))];
    }

    public function isLocalPath(string $url): bool
    {
        return str_starts_with($url, '/') && !preg_match('#^https?://#i', $url);
    }

    protected function ffprobe(string $path): array
    {
        $json = shell_exec('ffprobe -v error -show_entries format=duration:stream=width,height -of json ' . escapeshellarg($path) . ' 2>/dev/null');
        $data = json_decode((string) $json, true);
        $format = $data['format'] ?? [];
        $streams = $data['streams'] ?? [];
        return [
            'duration' => isset($format['duration']) ? (float) $format['duration'] : null,
            'width' => $streams[0]['width'] ?? null,
            'height' => $streams[0]['height'] ?? null,
            'thumbnail' => null,
            'title' => basename($path),
            'uploader' => null,
        ];
    }

    protected function ffprobeDuration(string $path): ?float
    {
        $json = shell_exec('ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 ' . escapeshellarg($path) . ' 2>/dev/null');
        if (!$json) return null;
        return (float) trim((string) $json);
    }

    protected function ffprobeWidth(string $path): ?int
    {
        $json = shell_exec('ffprobe -v error -show_entries stream=width -select_streams v:0 -of default=noprint_wrappers=1:nokey=1 ' . escapeshellarg($path) . ' 2>/dev/null');
        if (!$json) return null;
        return (int) trim((string) $json);
    }

    protected function ffprobeHeight(string $path): ?int
    {
        $json = shell_exec('ffprobe -v error -show_entries stream=height -select_streams v:0 -of default=noprint_wrappers=1:nokey=1 ' . escapeshellarg($path) . ' 2>/dev/null');
        if (!$json) return null;
        return (int) trim((string) $json);
    }

    protected function guessKind(string $path): string
    {
        if (str_contains($path, '/videos/preview-')) return 'preview';
        if (str_contains($path, '/videos/clip-')) return 'clip';
        if (str_contains($path, '/videos/export-')) return 'export';
        if (str_contains($path, '/videos/edit-')) return 'export';
        if (str_contains($path, '/videos/trim-')) return 'clip';
        if (str_starts_with($path, 'videos/')) return 'upload';
        return 'clip';
    }
}
