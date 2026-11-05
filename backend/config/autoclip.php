<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pembersihan Otomatis Media (AutoClip)
    |--------------------------------------------------------------------------
    |
    | Video sumber hasil unduhan (yt-dlp) dan file sementara FFmpeg menumpuk
    | di storage. Konfigurasi ini mengatur berapa lama file boleh disimpan
    | sebelum dihapus otomatis oleh command `autoclip:cleanup`.
    |
    */

    'cleanup' => [

        // Aktif/nonaktifkan pembersihan otomatis.
        'enabled' => env('AUTOCLIP_CLEANUP_ENABLED', true),

        // Umur file (menit) sebelum dihapus. Default: 60 menit (1 jam).
        'ttl_minutes' => (int) env('AUTOCLIP_CLEANUP_TTL_MINUTES', 60),

        // Hapus file sementara di storage/app/private
        // (src-*, preview-*, cut-*, edit-*, export-*, subs-*, concat-*, dll).
        'include_temp' => env('AUTOCLIP_CLEANUP_INCLUDE_TEMP', true),

        // Hapus video hasil publik di storage/app/public/videos
        // (preview, clip, export, trim, edit, dan video upload).
        'include_public_videos' => env('AUTOCLIP_CLEANUP_INCLUDE_PUBLIC_VIDEOS', true),

        // Hapus folder kosong setelah file dihapus.
        'delete_empty_dirs' => true,

        // Lokasi yang dipindai (di-resolve saat runtime).
        'paths' => [
            'temp' => storage_path('app/private'),
            'public_videos' => storage_path('app/public/videos'),
        ],

        // Ekstensi file yang boleh dihapus. File lain (mis. .gitignore,
        // gambar aset AI) tidak akan disentuh.
        'extensions' => [
            // video
            'mp4', 'mov', 'mkv', 'webm', 'avi', 'm4v', 'flv', 'wmv', 'mpg', 'mpeg', 'ts',
            // audio
            'mp3', 'wav', 'm4a', 'aac', 'flac', 'ogg', 'opus',
            // subtitle / file sementara ffmpeg
            'ass', 'srt', 'vtt', 'txt',
            // sisa unduhan yt-dlp
            'part', 'log', 'ytdl',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | yt-dlp
    |--------------------------------------------------------------------------
    |
    | Binary yt-dlp yang dipakai untuk mengunduh video dari URL. Pastikan
    | menggunakan versi terbaru karena situs seperti YouTube sering berubah
    | dan versi lama bisa ditolak (HTTP 403).
    |
    */

    'yt_dlp' => [
        'binary' => env('YT_DLP_BINARY', 'yt-dlp'),
    ],

];
