<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class GmailAuthController extends Controller
{
    public function redirectToGoogle(Request $request)
    {
        $secretFile = storage_path('app/gmail_client_secret.json');

        if (!file_exists($secretFile)) {
            return redirect()->route('admin.dashboard')->with('error', 'File credentials Gmail API tidak ditemukan di server.');
        }

        $secretData = json_decode(file_get_contents($secretFile), true)['web'] ?? [];
        if (empty($secretData)) {
            return redirect()->route('admin.dashboard')->with('error', 'Format file credentials Gmail API tidak valid.');
        }

        // Determine redirect URI dynamically
        $host = $request->getHost();
        $isLocal = str_contains($host, '127.0.0.1') || str_contains($host, 'localhost');
        $redirectUri = $isLocal ? 'http://127.0.0.1:8000/gmail/oauth/callback' : 'https://arhana.my.id/gmail/oauth/callback';

        $query = http_build_query([
            'client_id' => $secretData['client_id'],
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'https://www.googleapis.com/auth/gmail.send',
            'access_type' => 'offline',
            'prompt' => 'consent',
        ]);

        return redirect('https://accounts.google.com/o/oauth2/v2/auth?' . $query);
    }

    public function handleCallback(Request $request)
    {
        if (!$request->has('code')) {
            return redirect()->route('admin.dashboard')->with('error', 'Otorisasi Gmail dibatalkan oleh pengguna.');
        }

        try {
            $secretFile = storage_path('app/gmail_client_secret.json');
            if (!file_exists($secretFile)) {
                return redirect()->route('admin.dashboard')->with('error', 'File credentials Gmail API tidak ditemukan.');
            }

            $secretData = json_decode(file_get_contents($secretFile), true)['web'] ?? [];
            if (empty($secretData)) {
                return redirect()->route('admin.dashboard')->with('error', 'Format file credentials Gmail API tidak valid.');
            }

            $host = $request->getHost();
            $isLocal = str_contains($host, '127.0.0.1') || str_contains($host, 'localhost');
            $redirectUri = $isLocal ? 'http://127.0.0.1:8000/gmail/oauth/callback' : 'https://arhana.my.id/gmail/oauth/callback';

            $response = Http::withoutVerifying()->post('https://oauth2.googleapis.com/token', [
                'code' => $request->code,
                'client_id' => $secretData['client_id'],
                'client_secret' => $secretData['client_secret'],
                'redirect_uri' => $redirectUri,
                'grant_type' => 'authorization_code',
            ]);

            if ($response->failed()) {
                Log::error('Exchange OAuth Code Error:', ['body' => $response->body()]);
                $errorDesc = $response->json('error_description') ?? $response->json('error') ?? 'Error menukar kode';
                return redirect()->route('admin.dashboard')->with('error', 'Gagal menukarkan kode otorisasi Gmail: ' . $errorDesc);
            }

            $tokenData = $response->json();
            
            // Save token info
            $saveData = [
                'access_token' => $tokenData['access_token'],
                'refresh_token' => $tokenData['refresh_token'] ?? null, // refresh token is only sent on first consent
                'expires_at' => time() + ($tokenData['expires_in'] ?? 3600),
            ];

            $tokenFile = storage_path('app/gmail_token.json');

            // Check if there is an existing refresh_token we can preserve
            if (file_exists($tokenFile)) {
                $existing = json_decode(file_get_contents($tokenFile), true);
                if (empty($saveData['refresh_token']) && !empty($existing['refresh_token'])) {
                    $saveData['refresh_token'] = $existing['refresh_token'];
                }
            }

            if (empty($saveData['refresh_token'])) {
                return redirect()->route('admin.dashboard')->with('error', 'Peringatan: Refresh Token tidak didapatkan dari Google. Silakan hapus akses aplikasi ini di akun Google Anda lalu otorisasi ulang.');
            }

            // Ensure directory exists
            $dir = dirname($tokenFile);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            if (@file_put_contents($tokenFile, json_encode($saveData)) === false) {
                return redirect()->route('admin.dashboard')->with('error', 'Gagal menyimpan file token di server. Pastikan folder storage/app memiliki izin tulis.');
            }

            return redirect()->route('admin.dashboard')->with('success', 'Koneksi Gmail API OAuth 2.0 Berhasil Terhubung!');

        } catch (\Exception $e) {
            Log::error('Gmail OAuth Callback Exception:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->route('admin.dashboard')->with('error', 'Terjadi kesalahan sistem saat otorisasi: ' . $e->getMessage());
        }
    }

    public function disconnect()
    {
        $tokenFile = storage_path('app/gmail_token.json');
        if (file_exists($tokenFile)) {
            unlink($tokenFile);
        }
        return redirect()->route('admin.dashboard')->with('success', 'Koneksi Gmail API berhasil diputus.');
    }
}
