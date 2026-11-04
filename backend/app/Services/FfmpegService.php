<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

/**
 * Helper eksekusi FFmpeg + manajemen file sementara untuk pipeline Autoclip.
 */
class FfmpegService
{
    protected string $tempDir;

    public function __construct()
    {
        $this->tempDir = storage_path('app/private');
        if (!is_dir($this->tempDir)) {
            @mkdir($this->tempDir, 0777, true, true);
        }
    }

    public function tempPath(string $prefix = 'autoclip'): string
    {
        return rtrim($this->tempDir, '/\\') . DIRECTORY_SEPARATOR . $prefix . '-' . uniqid('', true) . '.mp4';
    }

    public function cleanup(string $path): void
    {
        if ($path && is_string($path) && file_exists($path)) {
            @unlink($path);
        }
    }

    /**
     * Ubah path lokal /storage/... atau path absolut menjadi path filesystem yang valid.
     */
    public function toLocalPath(string $source): string
    {
        if (str_starts_with($source, '/') && !preg_match('#^https?://#i', $source)) {
            if (str_starts_with($source, '/storage')) {
                return storage_path('app/public' . substr($source, strlen('/storage')));
            }
            return $source;
        }
        return $source;
    }

    /**
     * Resolve source menjadi path file lokal yang bisa dipakai ffmpeg.
     * - URL http(s) -> unduh via yt-dlp ke file temp.
     * - path lokal -> kembalikan path tersebut.
     */
    public function resolveSource(string $source): string
    {
        if (filter_var($source, FILTER_VALIDATE_URL)) {
            $dest = $this->tempPath('src');
            exec(
                'yt-dlp -f best -o ' . escapeshellarg($dest) . ' ' . escapeshellarg($source) . ' > /dev/null 2>&1',
                $output,
                $code
            );
            $files = glob(rtrim($this->tempDir, '/\\') . '/src-*');
            if ($code !== 0 || empty($files)) {
                throw new \RuntimeException('Gagal mengunduh source video dari URL tersebut.');
            }
            return $files[0];
        }

        $path = $this->toLocalPath($source);
        if (!is_file($path)) {
            throw new \RuntimeException('File video tidak ditemukan: ' . $source);
        }

        return $path;
    }

    /**
     * Potong video: ffmpeg -ss START -to END -i src ...
     */
    public function cut(string $source, float $start, float $end, array $settings = []): array
    {
        $src = $this->resolveSource($source);
        $out = $this->tempPath('cut');
        $cmd = 'ffmpeg -hide_banner -loglevel error -i ' . escapeshellarg($src) . ' -ss ' . round((float) $start, 2) . ' -to ' . round((float) $end, 2);
        if (!empty($settings['resolution'])) {
            $cmd .= ' -vf scale=' . (int) $settings['resolution'] . ':-2';
        }
        $cmd .= ' -c:v libx264 -preset fast -crf 23 -c:a aac -ar 48000 -ac 2 -movflags +faststart ' . escapeshellarg($out);
        exec($cmd, $output, $code);
        if ($code !== 0 || !is_file($out)) {
            $err = implode(' | ', array_slice($output, 0, 15));
            throw new \RuntimeException('FFmpeg gagal memotong video. ' . $err);
        }

        $duration = max(0.0, (float) $end - (float) $start);

        return ['file' => $out, 'duration' => $duration];
    }

    /**
     * Buat preview potongan (kualitas rendah) dari source.
     */
    public function preview(string $source, float $start, float $end): array
    {
        $src = $this->resolveSource($source);
        $out = $this->tempPath('preview');
        $cmd = 'ffmpeg -hide_banner -loglevel error -ss ' . round((float) $start, 2) . ' -i ' . escapeshellarg($src) . ' -to ' . round((float) $end, 2) . ' -an -c:v libx264 -preset fast -crf 23 -c:a aac -ar 48000 -ac 2 ' . escapeshellarg($out);
        exec($cmd, $output, $code);
        if ($code !== 0 || !is_file($out)) {
            $err = implode(' | ', array_slice($output, 0, 15));
            throw new \RuntimeException('FFmpeg gagal membuat preview. ' . $err);
        }

        return ['file' => $out, 'duration' => max(0.0, (float) $end - (float) $start)];
    }

    /**
     * Gabungkan beberapa file video via concat demuxer.
     */
    public function concat(array $files, string $out): bool
    {
        $listFile = $this->tempPath('concat') . '.txt';
        $content = '';
        foreach ($files as $f) {
            $content .= 'file ' . escapeshellarg($f) . "\n";
        }
        file_put_contents($listFile, $content);
        exec('ffmpeg -hide_banner -loglevel error -f concat -safe 0 -i ' . escapeshellarg($listFile) . ' -c copy ' . escapeshellarg($out));
        return is_file($out);
    }

    /**
     * Ubah path file lokal menjadi URL public (/storage/...).
     */
    public function toPublicUrl(string $tempFile, string $dir = 'videos'): string
    {
        if (!is_file($tempFile)) {
            throw new \RuntimeException('File hasil ffmpeg tidak ditemukan.');
        }
        $ext = pathinfo($tempFile, PATHINFO_EXTENSION);
        $dest = rtrim($dir, '/') . '/' . uniqid('autoclip_') . '.' . $ext;
        Storage::disk('public')->put($dest, file_get_contents($tempFile));
        return '/storage/' . $dest;
    }
}
