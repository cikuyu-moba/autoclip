<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeoIpService
{
    /**
     * Resolve geolocation for a given IP address.
     * Cached automatically for 30 days.
     */
    public static function lookup(?string $ip): array
    {
        if (empty($ip)) {
            return self::defaultLocation();
        }

        // Clean IPv6 mapped IPv4 or trim
        $ip = trim($ip);

        // 1. Check for localhost & loopback
        if ($ip === '127.0.0.1' || $ip === '::1' || $ip === 'localhost') {
            return [
                'city' => 'Lokal (Dev)',
                'region' => 'Localhost',
                'country' => 'Localhost',
                'country_code' => 'LOCAL',
            ];
        }

        // 2. Check for private/reserved IP ranges
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return [
                'city' => 'Jaringan Privat',
                'region' => 'LAN',
                'country' => 'LAN / Privat',
                'country_code' => 'LOCAL',
            ];
        }

        // 3. Cache lookup
        $cacheKey = 'geoip_' . md5($ip);
        return Cache::remember($cacheKey, now()->addDays(30), function () use ($ip) {
            try {
                // Primary service: ip-api.com (free, reliable, fast)
                $response = Http::timeout(2)
                    ->withoutVerifying()
                    ->get("http://ip-api.com/json/{$ip}?fields=status,message,country,countryCode,regionName,city");

                if ($response->successful()) {
                    $data = $response->json();
                    if (($data['status'] ?? '') === 'success') {
                        return [
                            'city' => !empty($data['city']) ? $data['city'] : null,
                            'region' => !empty($data['regionName']) ? $data['regionName'] : null,
                            'country' => !empty($data['country']) ? $data['country'] : 'Tidak Diketahui',
                            'country_code' => !empty($data['countryCode']) ? strtoupper($data['countryCode']) : null,
                        ];
                    }
                }

                // Fallback service: ipapi.co
                $fallback = Http::timeout(2)
                    ->withoutVerifying()
                    ->get("https://ipapi.co/{$ip}/json/");

                if ($fallback->successful()) {
                    $fbData = $fallback->json();
                    if (!empty($fbData['country_name'])) {
                        return [
                            'city' => !empty($fbData['city']) ? $fbData['city'] : null,
                            'region' => !empty($fbData['region']) ? $fbData['region'] : null,
                            'country' => $fbData['country_name'],
                            'country_code' => !empty($fbData['country_code']) ? strtoupper($fbData['country_code']) : null,
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::debug("GeoIP lookup failed for IP {$ip}: " . $e->getMessage());
            }

            return self::defaultLocation();
        });
    }

    /**
     * Convert 2-letter ISO country code into emoji flag.
     */
    public static function countryFlag(?string $countryCode): string
    {
        if (empty($countryCode)) {
            return '🌐';
        }

        $code = strtoupper(trim($countryCode));

        if ($code === 'LOCAL') {
            return '🏠';
        }

        if (strlen($code) === 2 && ctype_alpha($code)) {
            return mb_chr(127397 + ord($code[0])) . mb_chr(127397 + ord($code[1]));
        }

        return '🌐';
    }

    /**
     * Default unknown location payload.
     */
    protected static function defaultLocation(): array
    {
        return [
            'city' => null,
            'region' => null,
            'country' => 'Tidak Diketahui',
            'country_code' => null,
        ];
    }
}
