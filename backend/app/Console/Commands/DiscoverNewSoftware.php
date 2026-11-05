<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Software;
use App\Models\Category;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DiscoverNewSoftware extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'software:discover {--limit=3 : Number of new softwares to discover and add} {--repo= : Specific GitHub repo (owner/repo) to discover and add}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically discover and ingest new free and open source software (Windows, Mac, Linux, Android) from GitHub Releases';

    /**
     * Curated catalog of candidate open-source software (cross-platform & mobile)
     */
    protected array $candidates = [
        [
            'repo' => 'FreeTubeApp/FreeTube',
            'title' => 'FreeTube',
            'developer' => 'FreeTube Community',
            'category_slug' => 'multimedia-audio',
            'license' => 'Open Source',
            'description' => 'Aplikasi pemutar video YouTube open source pribadi untuk Windows, Mac, dan Linux tanpa iklan, tanpa pelacak data, dan dapat berlangganan channel tanpa akun Google.',
        ],
        [
            'repo' => 'microsoft/PowerToys',
            'title' => 'Microsoft PowerToys',
            'developer' => 'Microsoft',
            'category_slug' => 'tools-utilitas',
            'license' => 'MIT License',
            'description' => 'Kumpulan utilitas produktivitas resmi dari Microsoft untuk Windows guna menyesuaikan dan mempercepat alur kerja harian (FancyZones, Color Picker, Text Extractor, dll).',
        ],
        [
            'repo' => 'flow-launcher/Flow.Launcher',
            'title' => 'Flow Launcher',
            'developer' => 'Flow Launcher Team',
            'category_slug' => 'tools-utilitas',
            'license' => 'MIT License',
            'description' => 'Pencari file cepat dan peluncur aplikasi untuk Windows dengan navigasi keyboard intuitif, dukungan plugin, dan pencarian web instan.',
        ],
        [
            'repo' => 'files-community/Files',
            'title' => 'Files App',
            'developer' => 'Files Community',
            'category_slug' => 'tools-utilitas',
            'license' => 'Open Source',
            'description' => 'Pengganti Windows Explorer modern dengan antarmuka tab, dukungan cloud drive, tag warna, dan desain Fluent yang indah.',
        ],
        [
            'repo' => 'QL-Win/QuickLook',
            'title' => 'QuickLook',
            'developer' => 'Paddy Xu',
            'category_slug' => 'tools-utilitas',
            'license' => 'GPL-3.0',
            'description' => 'Membawa fitur Quick Look macOS ke Windows untuk melihat pratinjau instan dokumen, gambar, PDF, dan video hanya dengan menekan tombol Spasi.',
        ],
        [
            'repo' => 'flameshot-org/flameshot',
            'title' => 'Flameshot',
            'developer' => 'Flameshot Org',
            'category_slug' => 'tools-utilitas',
            'license' => 'GPL-3.0',
            'description' => 'Alat tangkapan layar canggih dan mudah dikonfigurasi dengan anotasi gambar in-place untuk Windows, macOS, dan Linux.',
        ],
        [
            'repo' => 'ventoy/Ventoy',
            'title' => 'Ventoy',
            'developer' => 'longpanda',
            'category_slug' => 'tools-utilitas',
            'license' => 'GPL-3.0',
            'description' => 'Tool pembuat USB bootable revolusioner. Cukup salin file ISO/WIM/IMG ke USB tanpa perlu memformat ulang flashdisk berulang kali.',
        ],
        [
            'repo' => 'balena-io/etcher',
            'title' => 'balenaEtcher',
            'developer' => 'balena',
            'category_slug' => 'tools-utilitas',
            'license' => 'Apache-2.0',
            'description' => 'Flasher image OS ke kartu SD dan USB drive yang aman, terverifikasi, dan mudah digunakan di Windows, macOS, dan Linux.',
        ],
        [
            'repo' => 'qarmin/czkawka',
            'title' => 'Czkawka',
            'developer' => 'Rafał Mikrut',
            'category_slug' => 'tools-utilitas',
            'license' => 'MIT License',
            'description' => 'Aplikasi multi-fungsi super cepat untuk membersihkan file duplikat, gambar serupa, file kosong, dan cache lama di Windows dan Linux.',
        ],
        [
            'repo' => 'hluk/CopyQ',
            'title' => 'CopyQ',
            'developer' => 'Lukáš Holeček',
            'category_slug' => 'tools-utilitas',
            'license' => 'GPL-3.0',
            'description' => 'Clipboard manager canggih dengan riwayat pencarian yang dapat diedit, mendukung teks, html, dan gambar untuk Windows, Mac, dan Linux.',
        ],
        [
            'repo' => 'TeamNewPipe/NewPipe',
            'title' => 'NewPipe',
            'developer' => 'Team NewPipe',
            'category_slug' => 'multimedia-audio',
            'license' => 'GPL-3.0',
            'description' => 'Frontend YouTube ringan untuk Android tanpa dependensi Google Play Services, bebas iklan, mendukung pemutaran latar belakang dan unduhan.',
        ],
        [
            'repo' => 'termux/termux-app',
            'title' => 'Termux',
            'developer' => 'Termux Team',
            'category_slug' => 'developer-tools',
            'license' => 'GPL-3.0',
            'description' => 'Emulator terminal Android dan lingkungan Linux yang kuat tanpa memerlukan akses root dengan ribuan paket paket perangkat lunak.',
        ],
        [
            'repo' => 'koreader/koreader',
            'title' => 'KOReader',
            'developer' => 'KOReader Team',
            'category_slug' => 'office-dokumen',
            'license' => 'AGPL-3.0',
            'description' => 'Aplikasi pembaca eBook dan dokumen untuk Android dan Linux dengan dukungan format PDF, EPUB, DJVU, CBZ, dan FB2.',
        ],
        [
            'repo' => 'ankidroid/Anki-Android',
            'title' => 'AnkiDroid',
            'developer' => 'AnkiDroid Open Source Team',
            'category_slug' => 'office-dokumen',
            'license' => 'GPL-3.0',
            'description' => 'Aplikasi flashcard berbasis spaced repetition untuk mengingat dan menghafal materi pelajaran, kosa kata bahasa asing, dan persiapan ujian di Android.',
        ],
        [
            'repo' => 'AntennaPod/AntennaPod',
            'title' => 'AntennaPod',
            'developer' => 'AntennaPod Community',
            'category_slug' => 'multimedia-audio',
            'license' => 'MIT License',
            'description' => 'Pemutar dan pengelola podcast open source terbaik untuk Android tanpa iklan, mendukung jutaan feed podcast gratis di seluruh dunia.',
        ],
        [
            'repo' => 'laurent22/joplin',
            'title' => 'Joplin',
            'developer' => 'Laurent Cozic',
            'category_slug' => 'office-dokumen',
            'license' => 'AGPL-3.0',
            'description' => 'Aplikasi pencatat dan to-do list aman dengan enkripsi end-to-end, format Markdown, dan sinkronisasi lintas platform (Windows, Mac, Linux, Android).',
        ],
        [
            'repo' => 'logseq/logseq',
            'title' => 'Logseq',
            'developer' => 'Logseq Inc.',
            'category_slug' => 'office-dokumen',
            'license' => 'AGPL-3.0',
            'description' => 'Platform manajemen pengetahuan berbasis graf dan privasi yang menyimpan semua catatan Anda secara lokal dalam format Markdown atau Org-mode.',
        ],
        [
            'repo' => 'AppFlowy-IO/AppFlowy',
            'title' => 'AppFlowy',
            'developer' => 'AppFlowy.IO',
            'category_slug' => 'office-dokumen',
            'license' => 'AGPL-3.0',
            'description' => 'Alternatif open source untuk Notion yang memberi kendali 100% atas data Anda untuk manajemen proyek, catatan, dan kolaborasi tim.',
        ],
        [
            'repo' => 'upscayl/upscayl',
            'title' => 'Upscayl',
            'developer' => 'Upscayl Team',
            'category_slug' => 'desain-grafis',
            'license' => 'AGPL-3.0',
            'description' => 'Aplikasi AI Image Upscaler gratis untuk meningkatkan resolusi gambar berkualitas rendah menjadi tajam dan jernih menggunakan AI canggih di komputer lokal.',
        ],
        [
            'repo' => 'xournalpp/xournalpp',
            'title' => 'Xournal++',
            'developer' => 'Xournal++ Team',
            'category_slug' => 'office-dokumen',
            'license' => 'GPL-2.0',
            'description' => 'Software pencatat tulisan tangan dan anotasi file PDF dengan dukungan stylus pen untuk Windows, macOS, dan Linux.',
        ],
        [
            'repo' => 'pdfarranger/pdfarranger',
            'title' => 'PDF Arranger',
            'developer' => 'PDF Arranger Team',
            'category_slug' => 'office-dokumen',
            'license' => 'GPL-3.0',
            'description' => 'Aplikasi grafis ringan untuk menggabungkan, memisahkan, memotong, merotasi, dan mengatur ulang halaman file PDF dengan mudah.',
        ],
        [
            'repo' => 'SubtitleEdit/subtitleedit',
            'title' => 'Subtitle Edit',
            'developer' => 'Nikolaj Lynge Olsson',
            'category_slug' => 'multimedia-audio',
            'license' => 'GPL-3.0',
            'description' => 'Editor subtitle video komprehensif untuk sinkronisasi teks, penyesuaian durasi waktu, terjemahan otomatis, dan konversi 300+ format subtitle.',
        ],
        [
            'repo' => 'mltframework/shotcut',
            'title' => 'Shotcut',
            'developer' => 'Meltytech, LLC',
            'category_slug' => 'multimedia-audio',
            'license' => 'GPL-3.0',
            'description' => 'Editor video open source lintas platform gratis dengan timeline multi-format, ratusan filter audio/video, dan dukungan resolusi hingga 4K.',
        ],
        [
            'repo' => 'OpenShot/OpenShot-v4',
            'title' => 'OpenShot Video Editor',
            'developer' => 'OpenShot Studios, LLC',
            'category_slug' => 'multimedia-audio',
            'license' => 'GPL-3.0',
            'description' => 'Editor video yang sangat mudah digunakan untuk pemula dengan fitur pemotong klip, efek transisi, animasi 3D, dan judul animasi.',
        ],
        [
            'repo' => 'VSCodium/vscodium',
            'title' => 'VSCodium',
            'developer' => 'VSCodium Community',
            'category_slug' => 'developer-tools',
            'license' => 'MIT License',
            'description' => 'Distribusi biner Visual Studio Code yang sepenuhnya gratis dan bersih dari telemetri pelacak Microsoft untuk Windows, Mac, dan Linux.',
        ],
        [
            'repo' => 'dbeaver/dbeaver',
            'title' => 'DBeaver',
            'developer' => 'DBeaver Corp',
            'category_slug' => 'developer-tools',
            'license' => 'Apache-2.0',
            'description' => 'Aplikasi pengelola database universal multi-platform gratis untuk MySQL, PostgreSQL, SQLite, Oracle, SQL Server, MongoDB, dan lainnya.',
        ],
        [
            'repo' => 'desktop/desktop',
            'title' => 'GitHub Desktop',
            'developer' => 'GitHub Inc.',
            'category_slug' => 'developer-tools',
            'license' => 'MIT License',
            'description' => 'Klien desktop Git resmi dari GitHub untuk mempermudah commit, branching, pull request, dan manajemen repositori dengan visual yang bersih.',
        ],
        [
            'repo' => 'neovim/neovim',
            'title' => 'Neovim',
            'developer' => 'Neovim Team',
            'category_slug' => 'developer-tools',
            'license' => 'Apache-2.0',
            'description' => 'Pembaruan modern dari teks editor Vim legendaris dengan performa tinggi, dukungan Lua script, dan ekosistem plugin yang kaya.',
        ],
        [
            'repo' => 'jesseduffield/lazygit',
            'title' => 'lazygit',
            'developer' => 'Jesse Duffield',
            'category_slug' => 'developer-tools',
            'license' => 'MIT License',
            'description' => 'Antarmuka terminal interaktif sederhana untuk perintah git yang mempermudah navigasi branch, stash, rebase, dan commit secara visual.',
        ],
        [
            'repo' => 'yt-dlp/yt-dlp',
            'title' => 'yt-dlp',
            'developer' => 'yt-dlp Team',
            'category_slug' => 'multimedia-audio',
            'license' => 'Unlicense',
            'description' => 'Alat pengunduh audio dan video serbaguna dari ribuan situs web video dengan fitur kustomisasi kualitas, subtitle, dan metadata lengkap.',
        ],
        [
            'repo' => 'cryptomator/cryptomator',
            'title' => 'Cryptomator',
            'developer' => 'Skymatic GmbH',
            'category_slug' => 'keamanan-privasi',
            'license' => 'GPL-3.0',
            'description' => 'Enkripsi client-side multiplatform untuk mengamankan file Anda sebelum diunggah ke Google Drive, Dropbox, OneDrive, atau cloud storage lainnya.',
        ],
        [
            'repo' => 'henrypp/simplewall',
            'title' => 'simplewall',
            'developer' => 'Henry++',
            'category_slug' => 'keamanan-privasi',
            'license' => 'GPL-3.0',
            'description' => 'Tool firewall portabel yang sangat ringan untuk mengonfigurasi Windows Filtering Platform (WFP) dan memblokir akses internet aplikasi mencurigakan.',
        ],
        [
            'repo' => 'streetcomplete/StreetComplete',
            'title' => 'StreetComplete',
            'developer' => 'StreetComplete Team',
            'category_slug' => 'tools-utilitas',
            'license' => 'GPL-3.0',
            'description' => 'Aplikasi kontributor peta OpenStreetMap untuk Android yang menyenangkan berupa kuis mini lapangan untuk melengkapi data peta di sekitar Anda.',
        ],
        [
            'repo' => 'MuntashirAkon/AppManager',
            'title' => 'App Manager',
            'developer' => 'Muntashir Akon',
            'category_slug' => 'tools-utilitas',
            'license' => 'GPL-3.0',
            'description' => 'Pengelola aplikasi Android tingkat lanjut untuk melihat izin keamanan, pelacak iklan pihak ketiga, mencadangkan APK, dan analisis aplikasi.',
        ],
        [
            'repo' => 'LibreCAD/LibreCAD',
            'title' => 'LibreCAD',
            'developer' => 'LibreCAD Community',
            'category_slug' => 'desain-grafis',
            'license' => 'GPL-2.0',
            'description' => 'Software CAD 2D komprehensif gratis untuk gambar teknik mesin, denah arsitektur, dan desain interior di Windows, Mac, dan Linux.',
        ],
        [
            'repo' => 'orama-interactive/pixelorama',
            'title' => 'Pixelorama',
            'developer' => 'Orama Interactive',
            'category_slug' => 'desain-grafis',
            'license' => 'MIT License',
            'description' => 'Editor sprite dan gambar seni piksel (pixel art) 2D gratis dengan dukungan timeline animasi, kuas khusus, dan palet warna luas.',
        ],
        [
            'repo' => 'ente-io/ente',
            'title' => 'Ente Photos',
            'developer' => 'Ente Technologies Inc.',
            'category_slug' => 'keamanan-privasi',
            'license' => 'AGPL-3.0',
            'description' => 'Alternatif Google Photos yang sepenuhnya open-source dengan enkripsi end-to-end menyeluruh untuk menyimpan foto dan video keluarga Anda secara aman.',
        ],
        [
            'repo' => 'jellyfin/jellyfin-media-player',
            'title' => 'Jellyfin Media Player',
            'developer' => 'Jellyfin Team',
            'category_slug' => 'multimedia-audio',
            'license' => 'GPL-2.0',
            'description' => 'Klien desktop resmi untuk sistem media server Jellyfin dengan kemampuan direct play tanpa transcoding untuk film, musik, dan serial TV.',
        ],
        [
            'repo' => 'zen-browser/desktop',
            'title' => 'Zen Browser',
            'developer' => 'Zen Browser Community',
            'category_slug' => 'browser-internet',
            'license' => 'MPL-2.0',
            'description' => 'Browser web berbasis Firefox yang indah dan modern dengan manajemen tab vertikal, split view, serta perlindungan privasi bawaan.',
        ],
        [
            'repo' => 'bitwarden/clients',
            'title' => 'Bitwarden',
            'developer' => 'Bitwarden Inc.',
            'category_slug' => 'keamanan-privasi',
            'license' => 'GPL-3.0',
            'description' => 'Manajer kata sandi open source paling tepercaya untuk menyimpan kata sandi, passkey, dan kartu kredit secara terenkripsi di seluruh perangkat Anda.',
        ],
        [
            'repo' => 'mullvad/mullvadvpn-app',
            'title' => 'Mullvad VPN App',
            'developer' => 'Mullvad VPN AB',
            'category_slug' => 'keamanan-privasi',
            'license' => 'GPL-3.0',
            'description' => 'Aplikasi klien resmi VPN Mullvad yang menjunjung tinggi privasi pengguna tanpa pencatatan aktivitas dan menggunakan protokol WireGuard modern.',
        ],
        [
            'repo' => 'nicotine-plus/nicotine-plus',
            'title' => 'Nicotine+',
            'developer' => 'Nicotine+ Team',
            'category_slug' => 'browser-internet',
            'license' => 'GPL-3.0',
            'description' => 'Klien grafis open source untuk jaringan berbagi file P2P Soulseek dengan antarmuka modern dan fitur pencarian audio berkualitas tinggi.',
        ],
        [
            'repo' => 'alacritty/alacritty',
            'title' => 'Alacritty',
            'developer' => 'Alacritty Team',
            'category_slug' => 'developer-tools',
            'license' => 'Apache-2.0',
            'description' => 'Emulator terminal berbasis GPU OpenGL lintas platform yang mengutamakan kecepatan respon super tinggi dan penggunaan memori yang efisien.',
        ],
        [
            'repo' => 'wez/wezterm',
            'title' => 'WezTerm',
            'developer' => 'Wez Furlong',
            'category_slug' => 'developer-tools',
            'license' => 'MIT License',
            'description' => 'Terminal emulator akselerasi GPU canggih dengan multiplexer bawaan, konfigurasi berbasis skrip Lua, dan dukungan font ligatur.',
        ],
        [
            'repo' => 'beekeeper-studio/beekeeper-studio',
            'title' => 'Beekeeper Studio',
            'developer' => 'Beekeeper Studio Team',
            'category_slug' => 'developer-tools',
            'license' => 'GPL-3.0',
            'description' => 'Editor SQL dan manajer database modern yang elegan, intuitif, dan nyaman digunakan untuk MySQL, Postgres, SQLite, dan SQL Server.',
        ],
        [
            'repo' => 'ActivityWatch/activitywatch',
            'title' => 'ActivityWatch',
            'developer' => 'ActivityWatch Team',
            'category_slug' => 'tools-utilitas',
            'license' => 'MPL-2.0',
            'description' => 'Aplikasi pelacak waktu otomatis dan produktivitas pribadi yang menjaga privasi Anda dengan menyimpan seluruh statistik di komputer lokal.',
        ],
        [
            'repo' => 'JabRef/jabref',
            'title' => 'JabRef',
            'developer' => 'JabRef Development Team',
            'category_slug' => 'office-dokumen',
            'license' => 'MIT License',
            'description' => 'Manajer sitasi dan referensi ilmiah berbasis format BibTeX untuk peneliti, akademisi, dan mahasiswa di Windows, Mac, dan Linux.',
        ],
        [
            'repo' => 'localsend/localsend',
            'title' => 'LocalSend',
            'developer' => 'Tien Do Nam',
            'category_slug' => 'tools-utilitas',
            'license' => 'Open Source',
            'description' => 'Aplikasi transfer file dan pesan lintas platform (Windows, macOS, Linux, Android, iOS) secara nirkabel melalui jaringan lokal tanpa internet, tanpa iklan, dan menjaga privasi penuh.',
        ],
        [
            'repo' => 'rustdesk/rustdesk',
            'title' => 'RustDesk',
            'developer' => 'Purslane Ltd',
            'category_slug' => 'tools-utilitas',
            'license' => 'Open Source',
            'description' => 'Software remote desktop gratis dan open source alternatif TeamViewer dan AnyDesk. Bekerja out-of-the-box tanpa konfigurasi rumit dengan kendali penuh atas data Anda.',
        ],
        [
            'repo' => 'Genymobile/scrcpy',
            'title' => 'scrcpy',
            'developer' => 'Genymobile',
            'category_slug' => 'developer-tools',
            'license' => 'Open Source',
            'description' => 'Aplikasi mirroring dan kendali layar HP Android dari komputer PC (Windows, Mac, Linux) via USB atau TCP/IP dengan latensi ultra rendah dan resolusi tinggi tanpa perlu root.',
        ],
        [
            'repo' => 'Heroic-Games-Launcher/HeroicGamesLauncher',
            'title' => 'Heroic Games Launcher',
            'developer' => 'Heroic Games Community',
            'category_slug' => 'tools-utilitas',
            'license' => 'Open Source',
            'description' => 'Game launcher open source untuk Epic Games, GOG, dan Amazon Games di Windows, macOS, dan Linux dengan dukungan integrasi Wine dan Proton.',
        ],
        [
            'repo' => 'peazip/PeaZip',
            'title' => 'PeaZip',
            'developer' => 'Giorgio Tani',
            'category_slug' => 'tools-utilitas',
            'license' => 'Open Source',
            'description' => 'Aplikasi manajer arsip dan kompresi file gratis open source alternatif WinRAR dengan dukungan lebih dari 200 format file untuk Windows, Mac, dan Linux.',
        ],
        [
            'repo' => 'bleachbit/bleachbit',
            'title' => 'BleachBit',
            'developer' => 'Andrew Ziem',
            'category_slug' => 'tools-utilitas',
            'license' => 'Open Source',
            'description' => 'Pembersih sistem dan ruang harddisk canggih alternatif CCleaner yang aman, menjaga privasi, dan bebas iklan untuk Windows dan Linux.',
        ],
        [
            'repo' => 'transmission/transmission',
            'title' => 'Transmission',
            'developer' => 'Transmission Project',
            'category_slug' => 'browser-internet',
            'license' => 'Open Source',
            'description' => 'Aplikasi BitTorrent client yang sangat ringan, hemat RAM, dan mudah digunakan tanpa iklan untuk Windows, Mac, dan Linux.',
        ],
        [
            'repo' => 'AutoHotkey/AutoHotkey',
            'title' => 'AutoHotkey',
            'developer' => 'AutoHotkey Foundation',
            'category_slug' => 'developer-tools',
            'license' => 'Open Source',
            'description' => 'Bahasa scripting otomatisasi gratis untuk membuat tombol pintas (hotkey), makro keyboard/mouse, dan otomasi pekerjaan di Windows.',
        ],
        [
            'repo' => 'FreeCAD/FreeCAD',
            'title' => 'FreeCAD',
            'developer' => 'FreeCAD Community',
            'category_slug' => 'desain-grafis',
            'license' => 'Open Source',
            'description' => 'Software pemodelan 3D parametrik CAD gratis untuk desain mekanik, arsitektur, dan rekayasa teknik di Windows, Mac, dan Linux.',
        ],
        [
            'repo' => 'mifi/lossless-cut',
            'title' => 'LosslessCut',
            'developer' => 'Mikael Finstad',
            'category_slug' => 'multimedia-audio',
            'license' => 'Open Source',
            'description' => 'Alat pemotong dan penggabung video super cepat tanpa proses re-encoding, menjaga kualitas video tetap jernih seketika untuk Windows, Mac, dan Linux.',
        ],
        [
            'repo' => 'greenshot/greenshot',
            'title' => 'Greenshot',
            'developer' => 'Greenshot Team',
            'category_slug' => 'tools-utilitas',
            'license' => 'Open Source',
            'description' => 'Tool screenshot ringan untuk Windows yang memudahkan tangkapan layar wilayah tertentu dan menambahkan anotasi teks/panah.',
        ],
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $headers = [
            'User-Agent' => 'Arhana-Software-Discoverer/2.0',
            'Accept' => 'application/vnd.github.v3+json',
        ];

        if (!empty(env('GITHUB_TOKEN'))) {
            $headers['Authorization'] = "token " . env('GITHUB_TOKEN');
        }

        // If specific repository is requested
        $specificRepo = $this->option('repo');
        if (!empty($specificRepo)) {
            $added = $this->handleSpecificRepo($specificRepo, $headers);
            return $added > 0 ? Command::SUCCESS : Command::FAILURE;
        }

        $limit = (int) $this->option('limit');
        $addedCount = 0;

        $this->info("Scanning candidate catalog for new software (Windows, Mac, Linux, Android)...\n");

        foreach ($this->candidates as $candidate) {
            if ($addedCount >= $limit) {
                break;
            }

            $slug = Str::slug($candidate['title']);

            // Check if already in database (by slug or repo)
            if (Software::where('slug', $slug)->orWhere('github_repo', $candidate['repo'])->exists()) {
                continue;
            }

            $this->line("<fg=cyan>Discovered candidate:</> {$candidate['title']} ({$candidate['repo']})");

            try {
                $releaseData = $this->fetchReleaseData($candidate['repo'], $headers);

                if (!$releaseData) {
                    $this->warn("   -> Skip: unable to fetch release from GitHub API");
                    continue;
                }

                $tagName = $releaseData['tag_name'] ?? $releaseData['name'] ?? '1.0.0';
                $cleanVersion = preg_replace('/^(v|release-?)/i', '', trim($tagName));

                $assets = $releaseData['assets'] ?? [];
                $detected = $this->detectMultiOsAssets($assets);

                $bestAsset = $detected['windows_asset'] ?? $detected['first_asset'];
                $downloadUrl = $bestAsset ? $bestAsset['browser_download_url'] : ($detected['os_downloads'][array_key_first($detected['os_downloads'])] ?? $releaseData['html_url'] ?? "https://github.com/{$candidate['repo']}/releases");
                $fileSize = ($bestAsset && !empty($bestAsset['size'])) ? round($bestAsset['size'] / (1024 * 1024), 1) . ' MB' : 'Gratis';

                // Resolve Category
                $category = Category::where('slug', $candidate['category_slug'])->where('type', 'software')->first();
                if (!$category) {
                    $category = Category::where('type', 'software')->first();
                }

                // Download icon if available
                $iconPath = $this->resolveAndSaveIcon($slug, $candidate['repo']);

                $osList = $detected['supported_os'];
                $osNames = array_map(fn($o) => match($o) {
                    'mac' => 'macOS',
                    'linux' => 'Linux',
                    'android' => 'Android',
                    default => 'Windows 10 / 11',
                }, $osList);
                $osCompatibility = implode(', ', $osNames);

                Software::create([
                    'title' => $candidate['title'],
                    'slug' => $slug,
                    'category_id' => $category?->id,
                    'developer' => $candidate['developer'],
                    'version' => $cleanVersion,
                    'license' => $candidate['license'],
                    'os_compatibility' => $osCompatibility,
                    'supported_os' => $osList,
                    'os_downloads' => !empty($detected['os_downloads']) ? $detected['os_downloads'] : null,
                    'file_size' => $fileSize,
                    'download_url' => $downloadUrl,
                    'official_website' => "https://github.com/{$candidate['repo']}",
                    'icon' => $iconPath,
                    'description' => $candidate['description'],
                    'features' => "<h3>Fitur Utama {$candidate['title']}</h3>\n<ul>\n<li>Software open-source gratis tanpa biaya lisensi.</li>\n<li>Bebas iklan, malware, dan pelacak privasi.</li>\n<li>Mendukung sistem operasi " . implode(', ', $osNames) . ".</li>\n</ul>",
                    'github_repo' => $candidate['repo'],
                    'sync_source' => 'github',
                    'last_synced_at' => now(),
                    'is_featured' => false,
                    'is_active' => true,
                    'views_count' => 0,
                    'downloads_count' => 0,
                ]);

                $osSummary = implode(', ', array_map('strtoupper', $osList));
                $this->info("   -> <fg=green>SUCCESS:</> Added {$candidate['title']} v{$cleanVersion} [OS: {$osSummary}]");
                $addedCount++;

            } catch (\Exception $e) {
                $this->error("   -> Error: " . $e->getMessage());
            }

            usleep(200000);
        }

        $this->newLine();
        $this->info("Discovery completed. Added {$addedCount} new software(s) to catalog.");

        return Command::SUCCESS;
    }

    /**
     * Ingest a specific GitHub repository on demand.
     */
    protected function handleSpecificRepo(string $rawRepo, array $headers): int
    {
        $repo = trim($rawRepo);
        $repo = preg_replace('#^https?://github\.com/#i', '', $repo);
        $repo = trim($repo, '/');
        $repo = preg_replace('/\.git$/i', '', $repo);

        if (empty($repo) || !str_contains($repo, '/')) {
            $this->error("Format repositori tidak valid: '{$rawRepo}'. Gunakan format 'owner/repo'.");
            return 0;
        }

        if (Software::where('github_repo', $repo)->exists()) {
            $this->warn("Software dari repositori '{$repo}' sudah ada di katalog.");
            return 0;
        }

        $this->line("<fg=cyan>Fetching repository info:</> {$repo}");

        try {
            // Fetch repository details from GitHub API
            $repoRes = Http::withHeaders($headers)->timeout(12)->get("https://api.github.com/repos/{$repo}");

            if ($repoRes->status() === 403) {
                $this->warn("GitHub API Rate Limit reached! Coba beberapa saat lagi atau atur GITHUB_TOKEN di .env.");
                return 0;
            }

            if (!$repoRes->successful()) {
                $this->error("Gagal mengambil data repositori dari GitHub API (HTTP {$repoRes->status()}).");
                return 0;
            }

            $repoData = $repoRes->json();
            $title = $repoData['name'] ?? explode('/', $repo)[1];
            $title = ucwords(str_replace(['-', '_'], ' ', $title));
            $slug = Str::slug($title);

            if (Software::where('slug', $slug)->exists()) {
                $slug = $slug . '-' . Str::random(4);
            }

            // Fetch release data
            $releaseData = $this->fetchReleaseData($repo, $headers);
            $cleanVersion = '1.0.0';
            $assets = [];
            $downloadUrl = $repoData['html_url'] ?? "https://github.com/{$repo}";
            $fileSize = 'Gratis';

            if ($releaseData) {
                $tagName = $releaseData['tag_name'] ?? $releaseData['name'] ?? '1.0.0';
                $cleanVersion = preg_replace('/^(v|release-?)/i', '', trim($tagName));
                $assets = $releaseData['assets'] ?? [];
            }

            $detected = $this->detectMultiOsAssets($assets);
            $bestAsset = $detected['windows_asset'] ?? $detected['first_asset'];

            if ($bestAsset) {
                $downloadUrl = $bestAsset['browser_download_url'];
                if (!empty($bestAsset['size'])) {
                    $fileSize = round($bestAsset['size'] / (1024 * 1024), 1) . ' MB';
                }
            } elseif (!empty($detected['os_downloads'])) {
                $downloadUrl = $detected['os_downloads'][array_key_first($detected['os_downloads'])];
            } elseif ($releaseData && !empty($releaseData['html_url'])) {
                $downloadUrl = $releaseData['html_url'];
            }

            $categorySlug = $this->guessCategorySlug($repoData);
            $category = Category::where('slug', $categorySlug)->where('type', 'software')->first();
            if (!$category) {
                $category = Category::where('type', 'software')->first();
            }

            $iconPath = $this->resolveAndSaveIcon($slug, $repo);

            $osList = $detected['supported_os'];
            $osNames = array_map(fn($o) => match($o) {
                'mac' => 'macOS',
                'linux' => 'Linux',
                'android' => 'Android',
                default => 'Windows 10 / 11',
            }, $osList);
            $osCompatibility = implode(', ', $osNames);

            $description = !empty($repoData['description']) ? $repoData['description'] : "Aplikasi open-source gratis {$title} untuk " . implode(', ', $osNames);

            $license = !empty($repoData['license']['name']) ? $repoData['license']['name'] : 'Open Source';
            $website = !empty($repoData['homepage']) ? $repoData['homepage'] : "https://github.com/{$repo}";

            Software::create([
                'title' => $title,
                'slug' => $slug,
                'category_id' => $category?->id,
                'developer' => $repoData['owner']['login'] ?? explode('/', $repo)[0],
                'version' => $cleanVersion,
                'license' => $license,
                'os_compatibility' => $osCompatibility,
                'supported_os' => $osList,
                'os_downloads' => !empty($detected['os_downloads']) ? $detected['os_downloads'] : null,
                'file_size' => $fileSize,
                'download_url' => $downloadUrl,
                'official_website' => $website,
                'icon' => $iconPath,
                'description' => $description,
                'features' => "<h3>Fitur Utama {$title}</h3>\n<ul>\n<li>Software open-source gratis tanpa lisensi berbayar.</li>\n<li>Bebas iklan, pelacak privasi, dan malware.</li>\n<li>Mendukung sistem operasi " . implode(', ', $osNames) . ".</li>\n</ul>",
                'github_repo' => $repo,
                'sync_source' => 'github',
                'last_synced_at' => now(),
                'is_featured' => false,
                'is_active' => true,
                'views_count' => 0,
                'downloads_count' => 0,
            ]);

            $this->info("   -> <fg=green>SUCCESS:</> Berhasil menambahkan {$title} v{$cleanVersion} dari repositori {$repo}");
            return 1;

        } catch (\Exception $e) {
            $this->error("   -> Error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Guess category slug based on repository name, description, and topics.
     */
    protected function guessCategorySlug(array $repoData): string
    {
        $text = strtolower(($repoData['name'] ?? '') . ' ' . ($repoData['description'] ?? '') . ' ' . implode(' ', $repoData['topics'] ?? []));

        if (preg_match('/(video|audio|music|media|player|sound|podcast|stream|youtube|mp4|mp3|recorder)/i', $text)) {
            return 'multimedia-audio';
        }
        if (preg_match('/(browser|download|torrent|p2p|network|web|internet|feed|rss)/i', $text)) {
            return 'browser-internet';
        }
        if (preg_match('/(dev|code|editor|ide|git|terminal|cli|api|sql|database|debug|compiler|programming)/i', $text)) {
            return 'developer-tools';
        }
        if (preg_match('/(graphic|cad|draw|paint|photo|image|pixel|3d|design|vector|svg)/i', $text)) {
            return 'desain-grafis';
        }
        if (preg_match('/(pdf|document|office|note|reader|book|text|markdown|todo|task)/i', $text)) {
            return 'office-dokumen';
        }
        if (preg_match('/(security|privacy|password|vpn|encrypt|firewall|antivirus|auth|crypto)/i', $text)) {
            return 'keamanan-privasi';
        }

        return 'tools-utilitas';
    }

    /**
     * Fetch release data trying /releases/latest first, falling back to /releases?per_page=1.
     */
    protected function fetchReleaseData(string $repo, array $headers): ?array
    {
        // 1. Try releases/latest
        try {
            $endpoint = "https://api.github.com/repos/{$repo}/releases/latest";
            $res = Http::withHeaders($headers)->timeout(12)->get($endpoint);
            if ($res->successful() && !empty($res->json())) {
                return $res->json();
            }
        } catch (\Exception $e) {
            // Continue to fallback
        }

        // 2. Fallback: /releases?per_page=1 (handles pre-releases and beta tags)
        try {
            $fallbackEndpoint = "https://api.github.com/repos/{$repo}/releases?per_page=1";
            $fallbackRes = Http::withHeaders($headers)->timeout(12)->get($fallbackEndpoint);
            if ($fallbackRes->successful()) {
                $data = $fallbackRes->json();
                if (is_array($data) && !empty($data[0])) {
                    return $data[0];
                }
            }
        } catch (\Exception $e) {
            // Continue
        }

        return null;
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

    /**
     * Detect assets for Windows, Mac, Linux, and Android from release assets.
     */
    protected function detectMultiOsAssets(array $assets): array
    {
        $osDownloads = [];
        $supportedOs = [];
        $windowsAsset = null;
        $firstAsset = null;

        foreach ($assets as $asset) {
            $name = strtolower($asset['name'] ?? '');
            $url = $asset['browser_download_url'] ?? '';

            if (empty($url)) {
                continue;
            }

            // Ignore checksums, signatures, source tarballs
            if (str_ends_with($name, '.sha256') || str_ends_with($name, '.asc') || 
                str_ends_with($name, '.sig') || str_ends_with($name, '.txt') || 
                str_ends_with($name, '.md') || str_ends_with($name, '.sha1') ||
                str_ends_with($name, '.sha512') || str_ends_with($name, '.blockmap') ||
                str_ends_with($name, '.json') || str_ends_with($name, '.yml') ||
                str_ends_with($name, '.yaml') || str_ends_with($name, '.zsync')) {
                continue;
            }

            if (!$firstAsset) {
                $firstAsset = $asset;
            }

            // 1. Android APK
            if (str_ends_with($name, '.apk')) {
                if (!isset($osDownloads['android'])) {
                    $osDownloads['android'] = $url;
                    $supportedOs[] = 'android';
                }
            }

            // 2. macOS (DMG, PKG, or mac zip)
            if (str_ends_with($name, '.dmg') || str_ends_with($name, '.pkg') || 
                ((str_ends_with($name, '.zip') || str_ends_with($name, '.tar.gz')) && 
                 (str_contains($name, 'mac') || str_contains($name, 'darwin') || str_contains($name, 'osx')))) {
                if (!isset($osDownloads['mac'])) {
                    $osDownloads['mac'] = $url;
                    $supportedOs[] = 'mac';
                }
            }

            // 3. Linux (AppImage, deb, rpm, or linux tar.gz)
            if (str_ends_with($name, '.appimage') || str_ends_with($name, '.deb') || str_ends_with($name, '.rpm') || 
                ((str_ends_with($name, '.tar.gz') || str_ends_with($name, '.zip')) && str_contains($name, 'linux'))) {
                if (!isset($osDownloads['linux'])) {
                    $osDownloads['linux'] = $url;
                    $supportedOs[] = 'linux';
                }
            }

            // 4. Windows (.exe, .msi, .msix, or win zip)
            if (str_ends_with($name, '.exe') || str_ends_with($name, '.msi') || str_ends_with($name, '.msix')) {
                if (str_contains($name, 'x64') || str_contains($name, 'win64') || str_contains($name, 'win') || str_contains($name, 'setup') || str_contains($name, 'installer')) {
                    $windowsAsset = $asset;
                    $osDownloads['windows'] = $url;
                    $supportedOs[] = 'windows';
                } elseif (!$windowsAsset) {
                    $windowsAsset = $asset;
                    $osDownloads['windows'] = $url;
                    $supportedOs[] = 'windows';
                }
            } elseif (str_ends_with($name, '.zip') && (str_contains($name, 'win') || str_contains($name, 'x64') || str_contains($name, 'windows'))) {
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
            'first_asset' => $firstAsset,
        ];
    }
}
