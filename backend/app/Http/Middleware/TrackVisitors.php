<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Visitor;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only track standard GET visits on public pages
        if ($request->isMethod('GET') 
            && !$request->ajax() 
            && !$request->is('admin*') 
            && !$request->is('api*')
            && !$request->is('storage*', 'build*', 'css*', 'js*', 'images*', 'logo*', 'favicon*', 'robots.txt', 'sitemap*')) {
            try {
                $ip = $request->ip();
                $path = '/' . ltrim($request->path(), '/');
                $pageName = $this->resolvePageName($request, $path);
                $location = \App\Services\GeoIpService::lookup($ip);
                $today = now()->toDateString();
                $userAgent = substr((string) $request->userAgent(), 0, 500);

                // 1. Update or create daily visitor record
                $visitor = Visitor::where('ip_address', $ip)
                    ->whereDate('date', $today)
                    ->first();

                if ($visitor) {
                    $visitor->hits = ($visitor->hits ?? 1) + 1;
                    $visitor->last_page = $pageName;
                    $visitor->user_agent = $userAgent;
                    if (empty($visitor->city) && !empty($location['city'])) {
                        $visitor->city = $location['city'];
                        $visitor->region = $location['region'];
                        $visitor->country = $location['country'];
                        $visitor->country_code = $location['country_code'];
                    }
                    $visitor->save();
                } else {
                    Visitor::create([
                        'ip_address' => $ip,
                        'date' => $today,
                        'city' => $location['city'] ?? null,
                        'region' => $location['region'] ?? null,
                        'country' => $location['country'] ?? null,
                        'country_code' => $location['country_code'] ?? null,
                        'last_page' => $pageName,
                        'hits' => 1,
                        'user_agent' => $userAgent,
                    ]);
                }

                // 2. Record detailed page visit log
                \App\Models\VisitorLog::create([
                    'ip_address' => $ip,
                    'url' => $path,
                    'page_name' => $pageName,
                    'method' => 'GET',
                    'city' => $location['city'] ?? null,
                    'region' => $location['region'] ?? null,
                    'country' => $location['country'] ?? null,
                    'country_code' => $location['country_code'] ?? null,
                    'user_agent' => $userAgent,
                    'date' => $today,
                ]);
            } catch (\Throwable $e) {
                // Fail-silent to ensure user browsing is NEVER interrupted if DB or lookup has issues
                report($e);
            }
        }

        return $next($request);
    }

    /**
     * Resolve a user-friendly page name from the request path.
     */
    protected function resolvePageName(Request $request, string $path): string
    {
        if ($path === '/' || $path === '') {
            return 'Beranda (Home)';
        }

        if ($request->is('portfolio*')) {
            return 'Portofolio Proyek';
        }

        if ($request->is('shop/*')) {
            $slug = $request->segment(2);
            $cleanName = ucwords(str_replace(['-', '_'], ' ', preg_replace('/^\d+-?/', '', $slug)));
            return 'Produk: ' . ($cleanName ?: $slug);
        }

        if ($request->is('shop*')) {
            return 'Katalog Toko / Produk';
        }

        if ($request->is('news/*')) {
            $slug = $request->segment(2);
            $cleanTitle = ucwords(str_replace(['-', '_'], ' ', $slug));
            return 'Berita: ' . $cleanTitle;
        }

        if ($request->is('news*')) {
            return 'Daftar Berita';
        }

        if ($request->is('courses/*')) {
            return 'Materi Kursus: ' . ucwords(str_replace(['-', '_'], ' ', (string) $request->segment(2)));
        }

        if ($request->is('courses*')) {
            return 'Daftar Kursus';
        }

        if ($request->is('booking*')) {
            return 'Halaman Booking Private';
        }

        return $path;
    }
}
