<?php

namespace App\Services;

use App\Models\ApiJob;

/**
 * Pemrosesan job pipeline Autoclip: clipping, edit overlay, export render.
 */
class ClipService
{
    public function run(string $type, ApiJob $job, array $data): array
    {
        return match ($type) {
            'preview' => $this->runPreview($job, $data),
            'clip' => $this->runClip($job, $data),
            'trim' => $this->runTrim($job, $data),
            'multi_clip' => $this->runMultiClip($job, $data),
            'edit' => $this->runEdit($job, $data),
            'export' => $this->runExport($job, $data),
            default => throw new \RuntimeException('Job type tidak dikenal: ' . $type),
        };
    }

    protected function runPreview(ApiJob $job, array $data): array
    {
        $job->update(['status' => 'processing', 'progress' => 5, 'message' => 'Menyiapkan source video...']);

        $source = (string) ($data['url'] ?? $data['video_url'] ?? '');
        $start = (float) ($data['start_time'] ?? 0);
        $end = (float) ($data['end_time'] ?? 30);

        $ffmpeg = app(FfmpegService::class);
        $section = $ffmpeg->downloadSection($source, $start, $end);
        if ($section !== null) {
            // File section sudah mulai dari 0 detik.
            $op = $ffmpeg->preview($section, 0, max(0.1, $end - $start));
        } else {
            $src = $ffmpeg->resolveSource($source);
            $op = $ffmpeg->preview($src, $start, $end);
        }

        $previewUrl = $ffmpeg->toPublicUrl($op['file'], 'videos/preview');
        $job->update([
            'status' => 'completed',
            'progress' => 100,
            'message' => 'Preview selesai',
            'result' => ['preview_url' => $previewUrl, 'duration' => $op['duration']],
        ]);

        return ['preview_url' => $previewUrl, 'duration' => $op['duration']];
    }

    protected function runClip(ApiJob $job, array $data): array
    {
        $job->update(['status' => 'processing', 'progress' => 5, 'message' => 'Menyiapkan source video...']);

        $source = (string) ($data['url'] ?? $data['video_url'] ?? '');
        $start = (float) ($data['start_time'] ?? 0);
        $end = (float) ($data['end_time'] ?? 30);

        $ffmpeg = app(FfmpegService::class);
        $section = $ffmpeg->downloadSection($source, $start, $end);
        if ($section !== null) {
            // File section sudah mulai dari 0 detik.
            $cut = $ffmpeg->cut($section, 0, max(0.1, $end - $start), [
                'resolution' => $data['resolution'] ?? null,
                'aspect_ratio' => $data['aspect_ratio'] ?? null,
                'video_bitrate' => $data['video_bitrate'] ?? null,
                'audio_bitrate' => $data['audio_bitrate'] ?? null,
            ]);
        } else {
            $src = $ffmpeg->resolveSource($source);
            $cut = $ffmpeg->cut($src, $start, $end, [
                'resolution' => $data['resolution'] ?? null,
                'aspect_ratio' => $data['aspect_ratio'] ?? null,
                'video_bitrate' => $data['video_bitrate'] ?? null,
                'audio_bitrate' => $data['audio_bitrate'] ?? null,
            ]);
        }
        $clipUrl = $ffmpeg->toPublicUrl($cut['file'], 'videos/clip');

        $job->update([
            'status' => 'completed',
            'progress' => 100,
            'message' => 'Potongan video selesai',
            'result' => ['clip_url' => $clipUrl, 'duration' => $cut['duration']],
        ]);

        return ['clip_url' => $clipUrl, 'duration' => $cut['duration']];
    }

    protected function runTrim(ApiJob $job, array $data): array
    {
        $job->update(['status' => 'processing', 'progress' => 5, 'message' => 'Memotong video dari source...']);

        $source = (string) ($data['video_url'] ?? '');
        $start = (float) ($data['start_time'] ?? 0);
        $end = (float) ($data['end_time'] ?? 30);

        $ffmpeg = app(FfmpegService::class);
        $section = $ffmpeg->downloadSection($source, $start, $end);
        if ($section !== null) {
            // File section sudah mulai dari 0 detik.
            $cut = $ffmpeg->cut($section, 0, max(0.1, $end - $start), [
                'resolution' => $data['resolution'] ?? null,
                'aspect_ratio' => $data['aspect_ratio'] ?? null,
                'video_bitrate' => $data['video_bitrate'] ?? null,
                'audio_bitrate' => $data['audio_bitrate'] ?? null,
            ]);
        } else {
            $src = $ffmpeg->resolveSource($source);
            $cut = $ffmpeg->cut($src, $start, $end, [
                'resolution' => $data['resolution'] ?? null,
                'aspect_ratio' => $data['aspect_ratio'] ?? null,
                'video_bitrate' => $data['video_bitrate'] ?? null,
                'audio_bitrate' => $data['audio_bitrate'] ?? null,
            ]);
        }
        $clipUrl = $ffmpeg->toPublicUrl($cut['file'], 'videos/clip');

        $job->update([
            'status' => 'completed',
            'progress' => 100,
            'message' => 'Potongan video selesai',
            'result' => ['clip_url' => $clipUrl, 'duration' => $cut['duration']],
        ]);

        return ['clip_url' => $clipUrl, 'duration' => $cut['duration']];
    }

    protected function runMultiClip(ApiJob $job, array $data): array
    {
        $job->update(['status' => 'processing', 'progress' => 5, 'message' => 'Menyiapkan source video...']);

        $source = (string) ($data['url'] ?? $data['video_url'] ?? '');
        $segments = is_array($data['segments'] ?? null) ? $data['segments'] : [];
        if (empty($segments)) {
            throw new \RuntimeException('Tidak ada segmen yang valid untuk diproses.');
        }

        $ffmpeg = app(FfmpegService::class);

        // Hitung rentang total segmen agar hanya bagian yang diperlukan yang diunduh.
        $minStart = null;
        $maxEnd = null;
        foreach ($segments as $seg) {
            $segStart = (float) ($seg['start_time'] ?? 0);
            $segEnd = (float) ($seg['end_time'] ?? $segStart + 1);
            $minStart = $minStart === null ? $segStart : min($minStart, $segStart);
            $maxEnd = $maxEnd === null ? $segEnd : max($maxEnd, $segEnd);
        }

        $section = $ffmpeg->downloadSection($source, (float) $minStart, (float) $maxEnd);
        if ($section !== null) {
            $src = $section;
            $offset = (float) $minStart;
        } else {
            $src = $ffmpeg->resolveSource($source);
            $offset = 0.0;
        }

        $files = [];
        $totalDuration = 0.0;

        foreach ($segments as $seg) {
            $origStart = (float) ($seg['start_time'] ?? 0);
            $origEnd = (float) ($seg['end_time'] ?? $origStart + 1);
            $start = max(0.0, $origStart - $offset);
            $end = max($start + 0.1, $origEnd - $offset);
            $cut = $ffmpeg->cut($src, $start, $end, ['resolution' => $data['resolution'] ?? null]);
            $files[] = $cut['file'];
            $totalDuration += (float) $cut['duration'];
        }

        $out = $ffmpeg->tempPath('multi');
        if (!$ffmpeg->concat($files, $out)) {
            foreach ($files as $f) { $ffmpeg->cleanup($f); }
            throw new \RuntimeException('Gagal menggabungkan segmen video.');
        }

        $clipUrl = $ffmpeg->toPublicUrl($out, 'videos/clip');
        foreach ($files as $f) { $ffmpeg->cleanup($f); }

        $job->update([
            'status' => 'completed',
            'progress' => 100,
            'message' => 'Rangkuman multi-clip selesai',
            'result' => ['clip_url' => $clipUrl, 'duration' => $totalDuration],
        ]);

        return ['clip_url' => $clipUrl, 'duration' => $totalDuration];
    }

    protected function runEdit(ApiJob $job, array $data): array
    {
        $job->update(['status' => 'processing', 'progress' => 5, 'message' => 'Mengerjakan render edit...']);

        $source = (string) ($data['video_url'] ?? '');
        $captions = is_array($data['captions'] ?? null) ? $data['captions'] : [];
        $style = is_array($data['style'] ?? null) ? $data['style'] : [];
        $stickers = is_array($data['stickers'] ?? null) ? $data['stickers'] : [];
        $frames = is_array($data['frames'] ?? null) ? $data['frames'] : [];

        $ffmpeg = app(FfmpegService::class);
        $src = $ffmpeg->resolveSource($source);

        $overlays = [];
        foreach (array_merge($stickers, $frames) as $overlay) {
            if (empty($overlay['src'])) continue;
            $overlays[] = [
                'src' => (string) $overlay['src'],
                'start' => (float) ($overlay['start_time'] ?? 0),
                'end' => $overlay['end_time'] ?? null,
                'x' => (float) ($overlay['x'] ?? 0.5),
                'y' => (float) ($overlay['y'] ?? 0.5),
                'width' => (float) ($overlay['width'] ?? 0.2),
            ];
        }

        $cmd = 'ffmpeg -hide_banner -loglevel error -i ' . escapeshellarg($src);
        $overlayInputs = [];
        foreach ($overlays as $o) {
            $ovFile = $this->tempOverlay($o);
            $cmd .= ' -loop 1 -i ' . escapeshellarg($ovFile);
            $overlayInputs[] = [
                'file' => $ovFile,
                'trim' => 'trim=start=' . round((float) $o['start'], 2) . ':end=' . (is_null($o['end']) ? '99999' : round((float) $o['end'], 2)) . ',setpts=PTS-STARTPTS',
                'enable' => 'between(t,' . round((float) $o['start'], 2) . ',' . (is_null($o['end']) ? '99999' : round((float) $o['end'], 2)) . ')',
                'x' => $o['x'],
                'y' => $o['y'],
                'w' => $o['width'],
                'h' => $o['width'],
            ];
        }

        $filter = '[0:v]format=yuv420p';
        if (!empty($captions)) {
            $assPath = $ffmpeg->tempPath('subs') . '.ass';
            file_put_contents($assPath, $this->buildAss($captions, $style));
            $filter .= ',subtitles=' . escapeshellarg($assPath) . '[vb]';
        } else {
            $filter .= ',null[vb]';
        }
        $base = 'vb';
        $finalLabel = 'vb';
        $n = count($overlayInputs);
        for ($i = 0; $i < $n; $i++) {
            $oi = $overlayInputs[$i];
            $filter .= ';[' . $base . '][' . (1 + $i) . ':v]' . $oi['trim'] . '[o' . $i . '];[' . $base . '][o' . $i . ']overlay=repeat=1:x=' . $this->num((float) $oi['x']) . ':y=' . $this->num((float) $oi['y']) . ':w=' . $this->num((float) $oi['w']) . ':h=' . $this->num((float) $oi['w']) . ':enable=' . escapeshellarg($oi['enable']) . '[v' . ($i + 2) . ']';
            $base = 'v' . ($i + 2);
            $finalLabel = $base;
        }
        if ($n > 0) {
            $filter .= ';[' . $finalLabel . ']';
        }

        $cmd .= ' -filter_complex "' . $filter . '" -map [' . $finalLabel . '] -frames:v 999999 -c:v libx264 -preset fast -crf 23 -c:a aac -ar 48000 -ac 2 -movflags +faststart ' . escapeshellarg($ffmpeg->tempPath('edit'));

        exec($cmd, $output, $code);
        if ($code !== 0) {
            $err = implode(' | ', array_slice($output, 0, 15));
            throw new \RuntimeException('FFmpeg gagal render edit. ' . $err);
        }
        $candidates = glob(rtrim($ffmpeg->tempDir, '/\\') . '/edit-*.mp4');
        $outFile = $candidates ? $candidates[count($candidates) - 1] : '';
        if (!$outFile || !is_file($outFile)) {
            throw new \RuntimeException('File hasil render edit tidak ditemukan.');
        }
        $downloadUrl = $ffmpeg->toPublicUrl($outFile, 'videos/export');
        $ffmpeg->cleanup($outFile);

        $job->update([
            'status' => 'completed',
            'progress' => 100,
            'message' => 'Render edit selesai',
            'result' => ['download_url' => $downloadUrl, 'duration' => (float) ($data['duration'] ?? 0)],
        ]);

        return ['download_url' => $downloadUrl];
    }

    protected function runExport(ApiJob $job, array $data): array
    {
        $job->update(['status' => 'processing', 'progress' => 5, 'message' => 'Merender video...']);

        $source = (string) ($data['video_url'] ?? '');
        $resolution = (int) ($data['resolution'] ?? 1080);
        $aspectRatio = (string) ($data['aspect_ratio'] ?? '9:16');
        $videoBitrate = (string) ($data['video_bitrate'] ?? '2500k');
        $audioBitrate = (string) ($data['audio_bitrate'] ?? '128k');
        $variation = is_array($data['variation'] ?? null) ? $data['variation'] : [];

        $ffmpeg = app(FfmpegService::class);
        $src = $ffmpeg->resolveSource($source);
        $out = $ffmpeg->tempPath('export');

        $targetW = (int) $resolution;
        $targetH = (int) round($targetW * (9 / 16));
        if (str_contains($aspectRatio, '16:9')) { $targetH = (int) round($targetW * (16 / 9)); }
        if (str_contains($aspectRatio, '1:1')) { $targetH = (int) round($targetW * 1); }
        if (str_contains($aspectRatio, '4:5')) { $targetH = (int) round($targetW * (5 / 4)); }

        $vf = 'scale=' . $targetW . ':' . $targetH . ',format=yuv420p';
        if (!empty($variation['video'])) {
            $vf .= ',hue=h=' . (int) ($variation['video']['hue_deg'] ?? 0);
        }

        $cmd = 'ffmpeg -hide_banner -loglevel error -i ' . escapeshellarg($src) . ' -vf "' . $vf . '" -c:v libx264 -preset fast -crf 23 -b:v ' . $this->bitrateToNumber($videoBitrate) . ' -c:a aac -b:a ' . $this->bitrateToNumber($audioBitrate) . ' -movflags +faststart ' . escapeshellarg($out);
        exec($cmd, $output, $code);
        if ($code !== 0 || !is_file($out)) {
            $err = implode(' | ', array_slice($output, 0, 15));
            throw new \RuntimeException('FFmpeg gagal render export. ' . $err);
        }

        $downloadUrl = $ffmpeg->toPublicUrl($out, 'videos/export');
        $ffmpeg->cleanup($out);

        $job->update([
            'status' => 'completed',
            'progress' => 100,
            'message' => 'Export video selesai',
            'result' => ['download_url' => $downloadUrl, 'duration' => (float) ($data['duration'] ?? 0), 'variation' => $variation],
        ]);

        return ['download_url' => $downloadUrl, 'duration' => (float) ($data['duration'] ?? 0), 'variation' => $variation];
    }

    protected function bitrateToNumber(string $bitr): string
    {
        return (string) (float) (preg_replace('/[^0-9.]/', '', (string) $bitr) ?: 2500);
    }

    protected function num(float $v): string
    {
        return (string) round($v);
    }

    protected function tempOverlay(array $o): string
    {
        $ffmpeg = app(FfmpegService::class);
        $file = $ffmpeg->tempPath('overlay');
        if (filter_var((string) ($o['src'] ?? ''), FILTER_VALIDATE_URL)) {
            exec('curl -sS ' . escapeshellarg((string) $o['src']) . ' -o ' . escapeshellarg($file));
        } else {
            $path = $ffmpeg->toLocalPath((string) $o['src']);
            if (!is_file($path)) {
                throw new \RuntimeException('File overlay tidak ditemukan: ' . $o['src']);
            }
            copy($path, $file);
        }
        return $file;
    }

    protected function buildAss(array $captions, array $style): string
    {
        $fontSize = (int) ($style['font_size'] ?? 72);
        $popUp = (bool) ($style['pop_up_effect'] ?? true);
        $marginV = (int) ($style['margin_v'] ?? 40);

        $script = ";FFmpeg generated ASS\n";
        $script .= "ScriptType: v4.00+\n";
        $script .= "PlayResX: 1080\n";
        $script .= "PlayResY: 1920\n";
        $script .= "ScaledBorderLess: 1\n";
        $script .= "[Events]\n";
        $script .= "Format: Layer, Start, End, Style, Name, Position, MarginL, MarginR, MarginV, Effect, Text\n";

        foreach ($captions as $c) {
            if (empty($c['text'])) continue;
            $start = (float) ($c['start'] ?? 0);
            $end = (float) ($c['end'] ?? $start + 2);
            $script .= 'Dialogue: 0 ' . $this->assTime($start) . ',' . $this->assTime($end) . ' Default,0,' . $fontSize . '%,0,0,0,0,' . ($popUp ? (string) $marginV : '0') . ',0,' . $this->assEscape((string) $c['text']) . "\n";
        }

        return $script;
    }

    protected function assTime(float $seconds): string
    {
        $h = (int) floor($seconds / 3600);
        $m = (int) floor(($seconds % 3600) / 60);
        $s = (int) floor($seconds % 60);
        $cs = (int) round((($seconds % 1) * 100));
        return sprintf('%02d:%02d:%02d.%02d', $h, $m, $s, $cs);
    }

    protected function assEscape(string $text): string
    {
        return str_replace('\\', '\\\\', str_replace(':', '\\:', $text));
    }
}
