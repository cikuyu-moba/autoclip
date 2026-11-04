<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PageView;
use App\Models\Visitor;
use App\Models\Post;
use App\Models\Product;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->query('start_date', now()->subDays(30)->toDateString());
        $endDate = $request->query('end_date', now()->toDateString());

        // Validate date inputs
        try {
            $validatedStart = \Carbon\Carbon::parse($startDate)->toDateString();
            $validatedEnd = \Carbon\Carbon::parse($endDate)->toDateString();
        } catch (\Exception $e) {
            $validatedStart = now()->subDays(30)->toDateString();
            $validatedEnd = now()->toDateString();
        }

        // 1. General unique visitors over the period (by day)
        $dailyVisitors = Visitor::whereDate('date', '>=', $validatedStart)
            ->whereDate('date', '<=', $validatedEnd)
            ->select('date', DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        $totalPeriodVisitors = Visitor::whereDate('date', '>=', $validatedStart)->whereDate('date', '<=', $validatedEnd)->count();
        $totalPeriodPageViews = PageView::whereDate('date', '>=', $validatedStart)->whereDate('date', '<=', $validatedEnd)->count();

        // 2. Most viewed Articles/News
        $popularPostViews = PageView::where('viewable_type', Post::class)
            ->whereBetween('date', [$validatedStart, $validatedEnd])
            ->select('viewable_id', DB::raw('count(*) as count'))
            ->groupBy('viewable_id')
            ->orderBy('count', 'desc')
            ->take(10)
            ->get();

        $popularPosts = [];
        foreach ($popularPostViews as $pv) {
            $post = Post::find($pv->viewable_id);
            if ($post) {
                $post->views_count = $pv->count;
                $popularPosts[] = $post;
            }
        }

        // 3. Most viewed Products/Goods
        $popularProductViews = PageView::where('viewable_type', Product::class)
            ->whereBetween('date', [$validatedStart, $validatedEnd])
            ->select('viewable_id', DB::raw('count(*) as count'))
            ->groupBy('viewable_id')
            ->orderBy('count', 'desc')
            ->take(10)
            ->get();

        $popularProducts = [];
        foreach ($popularProductViews as $pv) {
            $product = Product::find($pv->viewable_id);
            if ($product) {
                $product->views_count = $pv->count;
                $popularProducts[] = $product;
            }
        }

        // 4. Most viewed/clicked Projects
        $popularProjectViews = PageView::where('viewable_type', Project::class)
            ->whereBetween('date', [$validatedStart, $validatedEnd])
            ->select('viewable_id', DB::raw('count(*) as count'))
            ->groupBy('viewable_id')
            ->orderBy('count', 'desc')
            ->take(10)
            ->get();

        $popularProjects = [];
        foreach ($popularProjectViews as $pv) {
            $project = Project::find($pv->viewable_id);
            if ($project) {
                $project->views_count = $pv->count;
                $popularProjects[] = $project;
            }
        }

        // 5. Software Analytics: Most Downloaded & Most Viewed
        $popularSoftwareDownloads = \App\Models\Software::active()
            ->orderByDesc('downloads_count')
            ->orderByDesc('views_count')
            ->take(10)
            ->get();

        $popularSoftwareViews = \App\Models\Software::active()
            ->orderByDesc('views_count')
            ->orderByDesc('downloads_count')
            ->take(10)
            ->get();

        $softwareStats = [
            'total_software' => \App\Models\Software::count(),
            'total_downloads' => (int) \App\Models\Software::sum('downloads_count'),
            'total_views' => (int) \App\Models\Software::sum('views_count'),
        ];

        // 6. Visitor IP and Location Details (Paginated)
        $visitors = Visitor::whereDate('date', '>=', $validatedStart)
            ->whereDate('date', '<=', $validatedEnd)
            ->orderBy('updated_at', 'desc')
            ->paginate(15)
            ->appends($request->query());

        // Lazy-enrich location for visitors that don't have location yet
        foreach ($visitors as $v) {
            if (empty($v->country) || empty($v->city)) {
                $loc = \App\Services\GeoIpService::lookup($v->ip_address);
                $v->city = $loc['city'];
                $v->region = $loc['region'];
                $v->country = $loc['country'];
                $v->country_code = $loc['country_code'];
                $v->timestamps = false;
                $v->save();
            }
        }

        // 6. Top Countries & Top Cities
        $topCountries = Visitor::whereDate('date', '>=', $validatedStart)
            ->whereDate('date', '<=', $validatedEnd)
            ->whereNotNull('country')
            ->where('country', '!=', 'Tidak Diketahui')
            ->select('country', 'country_code', DB::raw('count(*) as count'))
            ->groupBy('country', 'country_code')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get();

        $topCities = Visitor::whereDate('date', '>=', $validatedStart)
            ->whereDate('date', '<=', $validatedEnd)
            ->whereNotNull('city')
            ->select('city', 'region', 'country', DB::raw('count(*) as count'))
            ->groupBy('city', 'region', 'country')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get();

        // 7. Recent Visitor Page Activity Stream
        $recentLogs = \App\Models\VisitorLog::whereDate('date', '>=', $validatedStart)
            ->whereDate('date', '<=', $validatedEnd)
            ->latest()
            ->take(10)
            ->get();

        return view('admin.reports.index', compact(
            'startDate',
            'endDate',
            'dailyVisitors',
            'totalPeriodVisitors',
            'popularPosts',
            'popularProducts',
            'popularProjects',
            'popularSoftwareDownloads',
            'popularSoftwareViews',
            'softwareStats',
            'visitors',
            'topCountries',
            'topCities',
            'recentLogs'
        ));
    }

    /**
     * AJAX Endpoint: Get all visited pages for a given IP address.
     */
    public function visitorLogs(Request $request)
    {
        $ip = trim((string) $request->query('ip'));
        if (empty($ip)) {
            return response()->json(['error' => 'IP address is required'], 400);
        }

        $logs = \App\Models\VisitorLog::where('ip_address', $ip)
            ->latest()
            ->take(50)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'url' => $log->url,
                    'page_name' => $log->page_name ?: $log->url,
                    'time' => $log->created_at ? $log->created_at->format('d M Y, H:i') : '-',
                    'time_ago' => $log->created_at ? $log->created_at->diffForHumans() : '',
                ];
            });

        $location = \App\Services\GeoIpService::lookup($ip);
        $flag = \App\Services\GeoIpService::countryFlag($location['country_code'] ?? null);
        $locationStr = implode(', ', array_filter([$location['city'] ?? null, $location['region'] ?? null, $location['country'] ?? null]));

        return response()->json([
            'ip' => $ip,
            'flag' => $flag,
            'location' => $locationStr ?: 'Tidak Diketahui',
            'total_logs' => $logs->count(),
            'logs' => $logs,
        ]);
    }
}
