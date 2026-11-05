<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Software;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SyncSoftwareReleases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'software:sync {--slug= : Slug of specific software to sync} {--force : Force sync even if recently updated}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync latest software release versions and download URLs for Windows, Mac, Linux, and Android from GitHub Releases API';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $slug = $this->option('slug');
        $query = Software::where('sync_source', 'github')
            ->whereNotNull('github_repo')
            ->where('github_repo', '!=', '');

        if ($slug) {
            $query->where('slug', $slug);
        }

        $softwares = $query->get();

        if ($softwares->isEmpty()) {
            $this->warn('No GitHub-synced software found to process.');
            return Command::SUCCESS;
        }

        $this->info("Found {$softwares->count()} software configured for GitHub sync. Starting sync...\n");

        $githubToken = env('GITHUB_TOKEN');
        $headers = [
            'User-Agent' => 'Arhana-Software-Updater/1.0',
            'Accept' => 'application/vnd.github.v3+json',
        ];

        if (!empty($githubToken)) {
            $headers['Authorization'] = "token {$githubToken}";
        }

        $successCount = 0;
        $failCount = 0;

        foreach ($softwares as $software) {
            $this->line("<fg=cyan>Processing:</> {$software->title} ({$software->github_repo})...");

            try {
                $endpoint = "https://api.github.com/repos/{$software->github_repo}/releases/latest";
                $response = Http::withHeaders($headers)->timeout(12)->get($endpoint);

                if ($response->status() === 403) {
                    // Rate limit hit: skip this software and keep processing the rest of the batch
                    // instead of aborting the entire sync run.
                    $this->warn("   -> GitHub API Rate Limit reached for {$software->title}. Skipping to next software. Try setting GITHUB_TOKEN in .env.");
                    $failCount++;
                    continue;
                }

                $data = null;
                if ($response->successful()) {
                    $data = $response->json();
                } elseif ($response->status() === 404) {
                    // Fallback to /releases?per_page=1 (handles beta / prerelease tags)
                    $fallbackEndpoint = "https://api.github.com/repos/{$software->github_repo}/releases?per_page=1";
                    $fallbackRes = Http::withHeaders($headers)->timeout(12)->get($fallbackEndpoint);
                    if ($fallbackRes->successful() && is_array($fallbackRes->json()) && !empty($fallbackRes->json()[0])) {
                        $data = $fallbackRes->json()[0];
                    }
                }

                if (!$data) {
                    $this->error("   -> Failed to fetch release from GitHub API. HTTP Status: " . $response->status());
                    $failCount++;
                    continue;
                }

                $tagName = $data['tag_name'] ?? $data['name'] ?? null;

                if (!$tagName) {
                    $this->warn("   -> No release tag found for {$software->title}");
                    $failCount++;
                    continue;
                }

                // Clean version string (strip leading "v" or "release-" prefixes)
                $cleanVersion = trim((string) preg_replace('/^(v|release-?)/i', '', trim($tagName)));

                if ($cleanVersion === '') {
                    $this->warn("   -> No release tag found for {$software->title}");
                    $failCount++;
                    continue;
                }

                $assets = $data['assets'] ?? [];
                $detected = $this->detectMultiOsAssets($assets);

                $updates = [
                    'version' => $cleanVersion,
                    'last_synced_at' => now(),
                ];

                // Only overwrite OS support / download links when the release provides usable assets,
                // so stale entries from previous releases never linger around.
                if (!empty($detected['windows_asset']) || !empty($detected['os_downloads'])) {
                    $updates['supported_os'] = $detected['supported_os'];
                    $updates['os_downloads'] = !empty($detected['os_downloads']) ? $detected['os_downloads'] : null;
                }

                $bestAsset = $detected['windows_asset'] ?? null;
                if ($bestAsset) {
                    $updates['download_url'] = $bestAsset['browser_download_url'];
                    if (!empty($bestAsset['size']) && $bestAsset['size'] > 0) {
                        $sizeMb = round($bestAsset['size'] / (1024 * 1024), 1);
                        $updates['file_size'] = "{$sizeMb} MB";
                    }
                } elseif (!empty($detected['os_downloads'])) {
                    $updates['download_url'] = $detected['os_downloads'][array_key_first($detected['os_downloads'])];
                } elseif (!empty($data['html_url'])) {
                    $updates['download_url'] = $data['html_url'];
                }

                // Auto-upgrade icon if still using default logo
                if (empty($software->icon) || $software->icon === 'logo/logo.png') {
                    $newIcon = $this->resolveAndSaveIcon($software->slug, $software->github_repo);
                    if ($newIcon !== 'logo/logo.png') {
                        $updates['icon'] = $newIcon;
                    }
                }

                $isChanged = $this->versionsDiffer($software->version, $cleanVersion);
                $software->update($updates);

                $osSummary = implode(', ', array_map('strtoupper', $detected['supported_os']));
                if ($isChanged) {
                    $this->info("   -> <fg=green>Updated:</> {$software->title} to version {$cleanVersion} [OS: {$osSummary}]");
                } else {
                    $this->line("   -> <fg=gray>Up-to-date:</> {$software->title} is version {$cleanVersion} [OS: {$osSummary}]");
                }

                $successCount++;
            } catch (\Exception $e) {
                $this->error("   -> Error: " . $e->getMessage());
                $failCount++;
            }

            usleep(250000);
        }

        $this->newLine();
        $this->info("Software sync completed! Successfully checked: {$successCount}, Failed: {$failCount}.");

        return Command::SUCCESS;
    }

    /**
     * Normalize a version string for comparison by stripping release prefixes
     * like "v", "release-" or app-name prefixes (e.g. "Audacity-4.0.2" -> "4.0.2").
     */
    protected function normalizeVersion($version): string
    {
        $version = strtolower(trim((string) ($version ?? '')));
        $version = (string) preg_replace('/^(?:v|release-?|[a-z][a-z0-9]*-)/', '', $version);

        return trim($version);
    }

    /**
     * Determine whether the stored version differs from the freshly synced version.
     */
    protected function versionsDiffer($currentVersion, string $newVersion): bool
    {
        return $this->normalizeVersion($currentVersion) !== $this->normalizeVersion($newVersion);
    }

    /**
     * Detect assets for Windows, Mac, Linux, and Android from release assets.
     */
    protected function detectMultiOsAssets(array $assets): array
    {
        $osDownloads = [];
        $supportedOs = [];
        $windowsAsset = null;

        foreach ($assets as $asset) {
            $name = strtolower($asset['name'] ?? '');
            $url = $asset['browser_download_url'] ?? '';

            // Ignore checksums, signatures, source tarballs
            if (str_ends_with($name, '.sha256') || str_ends_with($name, '.asc') || str_ends_with($name, '.sig') || str_ends_with($name, '.txt') || str_ends_with($name, '.md') || str_ends_with($name, '.sha1')) {
                continue;
            }

            // 1. Android APK
            if (str_ends_with($name, '.apk')) {
                if (!isset($osDownloads['android'])) {
                    $osDownloads['android'] = $url;
                    $supportedOs[] = 'android';
                }
            }

            // 2. macOS (DMG, PKG, or mac zip)
            if (str_ends_with($name, '.dmg') || str_ends_with($name, '.pkg') || (str_ends_with($name, '.zip') && (str_contains($name, 'mac') || str_contains($name, 'darwin') || str_contains($name, 'osx')))) {
                if (!isset($osDownloads['mac'])) {
                    $osDownloads['mac'] = $url;
                    $supportedOs[] = 'mac';
                }
            }

            // 3. Linux (AppImage, deb, rpm, or linux tar.gz)
            if (str_ends_with($name, '.appimage') || str_ends_with($name, '.deb') || str_ends_with($name, '.rpm') || (str_ends_with($name, '.tar.gz') && str_contains($name, 'linux'))) {
                if (!isset($osDownloads['linux'])) {
                    $osDownloads['linux'] = $url;
                    $supportedOs[] = 'linux';
                }
            }

            // 4. Windows (.exe, .msi, or win zip)
            if (str_ends_with($name, '.exe') || str_ends_with($name, '.msi')) {
                if (str_contains($name, 'x64') || str_contains($name, 'win64') || str_contains($name, 'win')) {
                    $windowsAsset = $asset;
                    $osDownloads['windows'] = $url;
                    $supportedOs[] = 'windows';
                } elseif (!$windowsAsset) {
                    $windowsAsset = $asset;
                    $osDownloads['windows'] = $url;
                    $supportedOs[] = 'windows';
                }
            } elseif (str_ends_with($name, '.zip') && (str_contains($name, 'win') || str_contains($name, 'x64'))) {
                if (!$windowsAsset) {
                    $windowsAsset = $asset;
                    $osDownloads['windows'] = $url;
                    $supportedOs[] = 'windows';
                }
            }
        }

        if (empty($supportedOs)) {
            $supportedOs = ['windows'];
        }

        return [
            'supported_os' => array_values(array_unique($supportedOs)),
            'os_downloads' => $osDownloads,
            'windows_asset' => $windowsAsset,
        ];
    }

    /**
     * Resolve icon via SimpleIcons or GitHub Owner avatar, and save to public folder.
     */
    protected function resolveAndSaveIcon(string $slug, string $repo): string
    {
        $dir = public_path('images/software');
        if (!is_dir($dir)) {
            @mkdir($dir, 0755);
        }

        // 1. Check if local icon already exists
        foreach (['svg', 'png', 'webp', 'jpg'] as $ext) {
            if (file_exists("{$dir}/{$slug}.{$ext}")) {
                return "images/software/{$slug}.{$ext}";
            }
        }

        // 2. Try SimpleIcons SVG
        $simpleIconSlug = str_replace(['-', '_', '.'], '', strtolower($slug));
        try {
            $iconRes = Http::timeout(6)->get("https://cdn.simpleicons.org/{$simpleIconSlug}");
            if ($iconRes->successful() && strlen($iconRes->body()) > 50 && str_contains($iconRes->body(), '<svg')) {
                $path = "images/software/{$slug}.svg";
                @file_put_contents(public_path($path), $iconRes->body());
                return $path;
            }
        } catch (\Exception $e) {
        }

        // 3. Try GitHub Owner/Org Avatar
        $owner = explode('/', $repo)[0] ?? '';
        if (!empty($owner)) {
            try {
                $avatarRes = Http::withHeaders(['User-Agent' => 'Mozilla/5.0'])->timeout(6)->get("https://github.com/{$owner}.png?size=128");
                if ($avatarRes->successful() && strlen($avatarRes->body()) > 200) {
                    $path = "images/software/{$slug}.png";
                    @file_put_contents(public_path($path), $avatarRes->body());
                    return $path;
                }
            } catch (\Exception $e) {
            }
        }

        return 'logo/logo.png';
    }
}
