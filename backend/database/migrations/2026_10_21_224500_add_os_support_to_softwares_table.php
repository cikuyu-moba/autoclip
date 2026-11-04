<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Menu;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('softwares', function (Blueprint $table) {
            $table->json('supported_os')->nullable()->after('os_compatibility');
            $table->json('os_downloads')->nullable()->after('download_url');
        });

        // 1. Populate initial supported_os and os_downloads for existing software
        $osMap = [
            'vlc-media-player' => [
                'os' => ['windows', 'mac', 'linux', 'android'],
                'downloads' => [
                    'windows' => 'https://get.videolan.org/vlc/3.0.21/win64/vlc-3.0.21-win64.exe',
                    'mac' => 'https://get.videolan.org/vlc/3.0.21/macosx/vlc-3.0.21-intel64.dmg',
                    'linux' => 'https://www.videolan.org/vlc/#download',
                    'android' => 'https://play.google.com/store/apps/details?id=org.videolan.vlc',
                ],
            ],
            'brave-browser' => [
                'os' => ['windows', 'mac', 'linux', 'android'],
                'downloads' => [
                    'mac' => 'https://brave.com/download/',
                    'linux' => 'https://brave.com/linux/',
                    'android' => 'https://play.google.com/store/apps/details?id=com.brave.browser',
                ],
            ],
            'obs-studio' => [
                'os' => ['windows', 'mac', 'linux'],
                'downloads' => [
                    'mac' => 'https://obsproject.com/download',
                    'linux' => 'https://obsproject.com/download',
                ],
            ],
            'blender' => [
                'os' => ['windows', 'mac', 'linux'],
                'downloads' => [
                    'mac' => 'https://www.blender.org/download/',
                    'linux' => 'https://www.blender.org/download/',
                ],
            ],
            'visual-studio-code' => [
                'os' => ['windows', 'mac', 'linux'],
                'downloads' => [
                    'mac' => 'https://code.visualstudio.com/Download',
                    'linux' => 'https://code.visualstudio.com/Download',
                ],
            ],
            'gimp' => [
                'os' => ['windows', 'mac', 'linux'],
                'downloads' => [
                    'mac' => 'https://www.gimp.org/downloads/',
                    'linux' => 'https://www.gimp.org/downloads/',
                ],
            ],
            'audacity' => [
                'os' => ['windows', 'mac', 'linux'],
                'downloads' => [
                    'mac' => 'https://www.audacityteam.org/download/mac/',
                    'linux' => 'https://www.audacityteam.org/download/linux/',
                ],
            ],
            'handbrake' => [
                'os' => ['windows', 'mac', 'linux'],
                'downloads' => [
                    'mac' => 'https://handbrake.fr/downloads.php',
                    'linux' => 'https://handbrake.fr/downloads.php',
                ],
            ],
            'libreoffice' => [
                'os' => ['windows', 'mac', 'linux'],
                'downloads' => [
                    'mac' => 'https://www.libreoffice.org/download/download-libreoffice/',
                    'linux' => 'https://www.libreoffice.org/download/download-libreoffice/',
                ],
            ],
            'keepassxc' => [
                'os' => ['windows', 'mac', 'linux'],
                'downloads' => [
                    'mac' => 'https://keepassxc.org/download/#mac',
                    'linux' => 'https://keepassxc.org/download/#linux',
                ],
            ],
            'qbittorrent' => [
                'os' => ['windows', 'mac', 'linux'],
                'downloads' => [
                    'mac' => 'https://www.qbittorrent.org/download',
                    'linux' => 'https://www.qbittorrent.org/download',
                ],
            ],
            'peazip' => [
                'os' => ['windows', 'mac', 'linux'],
                'downloads' => [
                    'mac' => 'https://peazip.github.io/peazip-macos.html',
                    'linux' => 'https://peazip.github.io/peazip-linux.html',
                ],
            ],
            '7-zip' => [
                'os' => ['windows', 'linux'],
                'downloads' => [
                    'linux' => 'https://www.7-zip.org/download.html',
                ],
            ],
            'bleachbit' => [
                'os' => ['windows', 'linux'],
                'downloads' => [
                    'linux' => 'https://www.bleachbit.org/download/linux',
                ],
            ],
        ];

        // Update predefined softwares
        foreach ($osMap as $slug => $data) {
            DB::table('softwares')->where('slug', $slug)->update([
                'supported_os' => json_encode($data['os']),
                'os_downloads' => isset($data['downloads']) ? json_encode($data['downloads']) : null,
            ]);
        }

        // For all remaining softwares, default supported_os to ['windows']
        DB::table('softwares')->whereNull('supported_os')->update([
            'supported_os' => json_encode(['windows']),
        ]);

        // 2. Add Submenus to Navigation Menu "SOFTWARE" if exists
        try {
            $softwareMenu = Menu::where('url', '/software')->first();
            if ($softwareMenu) {
                $submenus = [
                    ['title' => 'Semua Software', 'url' => '/software', 'order' => 1],
                    ['title' => 'Software Windows', 'url' => '/software?os=windows', 'order' => 2],
                    ['title' => 'Software Mac', 'url' => '/software?os=mac', 'order' => 3],
                    ['title' => 'Software Linux', 'url' => '/software?os=linux', 'order' => 4],
                    ['title' => 'Aplikasi Android', 'url' => '/software?os=android', 'order' => 5],
                ];

                foreach ($submenus as $sub) {
                    Menu::firstOrCreate(
                        ['parent_id' => $softwareMenu->id, 'url' => $sub['url']],
                        ['title' => $sub['title'], 'order' => $sub['order'], 'is_external' => false]
                    );
                }
            }
        } catch (\Exception $e) {
            // Silently continue if menu table unavailable
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove submenus under SOFTWARE
        try {
            $softwareMenu = Menu::where('url', '/software')->first();
            if ($softwareMenu) {
                Menu::where('parent_id', $softwareMenu->id)->delete();
            }
        } catch (\Exception $e) {
            // Silently continue
        }

        Schema::table('softwares', function (Blueprint $table) {
            $table->dropColumn(['supported_os', 'os_downloads']);
        });
    }
};
