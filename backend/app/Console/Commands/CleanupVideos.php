<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * Menghapus video yang sudah diunduh/dibuat lebih dari batas waktu (default 1 jam)
 * untuk mengurangi pemakaian storage server.
 */
class CleanupVideos extends Command
{
    /**
     * @var string
     */
    protected $signature = 'autoclip:cleanup
        {--minutes= : Umur minimum file dalam menit sebelum dihapus (default dari config)}
        {--dry-run : Tampilkan file yang akan dihapus tanpa benar-benar menghapus}
        {--include-public : Paksa ikut menghapus video publik (preview/clip/export/upload)}
        {--temp-only : Hanya bersihkan file sementara di storage/app/private}';

    /**
     * @var string
     */
    protected $description = 'Hapus otomatis video unduhan dan file sementara AutoClip yang lebih tua dari TTL (default 1 jam)';

    public function handle(): int
    {
        $config = (array) config('autoclip.cleanup', []);

        $dryRun = (bool) $this->option('dry-run');

        if (!($config['enabled'] ?? true) && !$dryRun) {
            $this->warn('Pembersihan otomatis dinonaktifkan (AUTOCLIP_CLEANUP_ENABLED=false).');

            return self::SUCCESS;
        }

        $minutes = $this->option('minutes');
        $minutes = $minutes !== null ? (int) $minutes : (int) ($config['ttl_minutes'] ?? 60);
        $minutes = max(1, $minutes);

        $cutoff = time() - ($minutes * 60);

        $extensions = array_map(
            static fn ($ext) => strtolower(trim((string) $ext)),
            (array) ($config['extensions'] ?? [])
        );
        $extensions = array_values(array_filter($extensions));

        $paths = (array) ($config['paths'] ?? []);
        $targets = [];

        if (($config['include_temp'] ?? true) && !empty($paths['temp'])) {
            $targets['temp'] = $paths['temp'];
        }

        $includePublic = (bool) ($config['include_public_videos'] ?? true) || (bool) $this->option('include-public');

        if ($this->option('temp-only')) {
            $includePublic = false;
        }

        if ($includePublic && !empty($paths['public_videos'])) {
            $targets['public'] = $paths['public_videos'];
        }

        if ($targets === []) {
            $this->warn('Tidak ada direktori yang dikonfigurasi untuk dibersihkan.');

            return self::SUCCESS;
        }

        $this->info(sprintf(
            'Membersihkan file lebih tua dari %d menit (%s)%s...',
            $minutes,
            date('Y-m-d H:i:s', $cutoff),
            $dryRun ? ' [DRY RUN]' : ''
        ));

        $deletedCount = 0;
        $deletedBytes = 0;
        $failedCount = 0;
        $skippedCount = 0;
        $deletedFiles = [];

        foreach ($targets as $label => $path) {
            $path = rtrim((string) $path, '/\\');

            if (!is_dir($path)) {
                $this->line("  - {$label}: direktori tidak ditemukan ({$path})");
                continue;
            }

            $this->line("  - {$label}: {$path}");

            foreach ($this->scanFiles($path, $extensions) as $file) {
                /** @var SplFileInfo $file */
                $mtime = @filemtime($file->getPathname());

                if ($mtime === false || $mtime > $cutoff) {
                    $skippedCount++;
                    continue;
                }

                $size = (int) (@filesize($file->getPathname()) ?: 0);

                if ($dryRun) {
                    $deletedCount++;
                    $deletedBytes += $size;
                    $deletedFiles[] = $file->getPathname();
                    continue;
                }

                if ($this->deleteFile($file->getPathname())) {
                    $deletedCount++;
                    $deletedBytes += $size;
                    $deletedFiles[] = $file->getPathname();
                } else {
                    $failedCount++;
                    $this->warn('    Gagal menghapus: ' . $file->getPathname());
                }
            }

            if (($config['delete_empty_dirs'] ?? true) && !$dryRun) {
                $this->removeEmptyDirectories($path);
            }
        }

        if ($deletedFiles !== []) {
            $limit = 50;
            foreach (array_slice($deletedFiles, 0, $limit) as $file) {
                $this->line(($dryRun ? '    [akan dihapus] ' : '    dihapus: ') . $file);
            }
            if (count($deletedFiles) > $limit) {
                $this->line('    ... dan ' . (count($deletedFiles) - $limit) . ' file lainnya');
            }
        }

        $summary = sprintf(
            '%s %d file (%s), gagal %d, dilewati %d.',
            $dryRun ? 'Akan menghapus' : 'Menghapus',
            $deletedCount,
            $this->formatBytes($deletedBytes),
            $failedCount,
            $skippedCount
        );

        $this->info($summary);

        Log::info('[autoclip:cleanup] ' . $summary, [
            'minutes' => $minutes,
            'dry_run' => $dryRun,
            'targets' => array_keys($targets),
        ]);

        return $failedCount > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * @param  array<int, string>  $extensions
     * @return iterable<int, SplFileInfo>
     */
    protected function scanFiles(string $root, array $extensions): iterable
    {
        try {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(
                    $root,
                    RecursiveDirectoryIterator::SKIP_DOTS
                ),
                RecursiveIteratorIterator::LEAVES_ONLY,
                RecursiveIteratorIterator::CATCH_GET_CHILD
            );
        } catch (\Throwable $e) {
            $this->warn('    Tidak dapat membaca direktori: ' . $e->getMessage());

            return;
        }

        foreach ($iterator as $file) {
            /** @var SplFileInfo $file */
            if (!$file->isFile() || $file->isLink()) {
                continue;
            }

            $ext = strtolower($file->getExtension());

            if ($ext === '' || !in_array($ext, $extensions, true)) {
                continue;
            }

            yield $file;
        }
    }

    protected function deleteFile(string $path): bool
    {
        // Gunakan @ agar warning tidak memenuhi log; hasil dicek dari return value.
        return @unlink($path);
    }

    /**
     * Hapus folder kosong secara bottom-up, tanpa menghapus root.
     */
    protected function removeEmptyDirectories(string $root): void
    {
        $root = rtrim($root, '/\\');
        $directories = [];

        try {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($root, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST,
                RecursiveIteratorIterator::CATCH_GET_CHILD
            );

            foreach ($iterator as $item) {
                if ($item->isDir() && !$item->isLink()) {
                    $directories[] = $item->getPathname();
                }
            }
        } catch (\Throwable $e) {
            return;
        }

        foreach ($directories as $directory) {
            if (is_dir($directory) && !$this->directoryHasEntries($directory)) {
                @rmdir($directory);
            }
        }
    }

    protected function directoryHasEntries(string $directory): bool
    {
        $handle = @opendir($directory);

        if ($handle === false) {
            return true;
        }

        try {
            while (($entry = readdir($handle)) !== false) {
                if ($entry !== '.' && $entry !== '..' && $entry !== '.gitignore') {
                    return true;
                }
            }
        } finally {
            closedir($handle);
        }

        return false;
    }

    protected function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }

        $units = ['KB', 'MB', 'GB', 'TB'];
        $value = $bytes / 1024;
        $index = 0;

        while ($value >= 1024 && $index < count($units) - 1) {
            $value /= 1024;
            $index++;
        }

        return number_format($value, 2) . ' ' . $units[$index];
    }
}
