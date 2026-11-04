<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Software;
use App\Models\Category;

class SoftwarePublicController extends Controller
{
    /**
     * Display software catalog.
     */
    public function index(Request $request)
    {
        $categories = Category::where('type', 'software')->withCount('softwares')->get();

        $query = Software::with('category')->active();

        // Filter by Category
        if ($request->filled('category')) {
            $categorySlug = $request->query('category');
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        // Filter by OS (Windows, Mac, Linux, Android)
        if ($request->filled('os')) {
            $query->forOs($request->query('os'));
        }

        // Filter by Search
        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('developer', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Sorting
        $sort = $request->query('sort', 'updated');
        if ($sort === 'popular') {
            $query->orderByDesc('downloads_count')->orderByDesc('views_count');
        } elseif ($sort === 'name') {
            $query->orderBy('title', 'asc');
        } elseif ($sort === 'newest') {
            $query->latest();
        } else {
            // 'updated'
            $query->orderByRaw('COALESCE(last_synced_at, updated_at) DESC');
        }

        $softwares = $query->paginate(12)->withQueryString();

        // Count per OS
        $osCounts = [
            'all' => Software::active()->count(),
            'windows' => Software::active()->forOs('windows')->count(),
            'mac' => Software::active()->forOs('mac')->count(),
            'linux' => Software::active()->forOs('linux')->count(),
            'android' => Software::active()->forOs('android')->count(),
        ];

        // Top featured software for spotlight badge / section
        $featuredSoftwares = Software::active()->featured()->take(4)->get();

        return view('software.index', compact('softwares', 'categories', 'featuredSoftwares', 'osCounts'));
    }

    /**
     * Show software detail page.
     */
    public function show($slug)
    {
        $software = Software::where('slug', $slug)
            ->with(['category', 'comments.replies.user', 'comments.user'])
            ->active()
            ->firstOrFail();

        // Track page view
        $viewSessionKey = 'viewed_software_' . $software->id;
        if (!session()->has($viewSessionKey)) {
            $software->increment('views_count');
            session()->put($viewSessionKey, true);
        }

        try {
            \App\Models\PageView::insertOrIgnore([
                'ip_address' => request()->ip(),
                'viewable_id' => $software->id,
                'viewable_type' => \App\Models\Software::class,
                'date' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Silently continue
        }

        // Related software
        $relatedSoftwares = Software::active()
            ->where('id', '!=', $software->id)
            ->where('category_id', $software->category_id)
            ->take(4)
            ->get();

        if ($relatedSoftwares->isEmpty()) {
            $relatedSoftwares = Software::active()
                ->where('id', '!=', $software->id)
                ->inRandomOrder()
                ->take(4)
                ->get();
        }

        return view('software.show', compact('software', 'relatedSoftwares'));
    }

    /**
     * Store comment or software request.
     */
    public function storeComment(Request $request, $softwareId)
    {
        $software = Software::findOrFail($softwareId);

        $request->validate([
            'username' => 'nullable|string|max:50',
            'content' => 'required|string|max:1000',
            'type' => 'nullable|string|in:comment,request',
            'parent_id' => 'nullable|exists:software_comments,id',
        ]);

        $username = $request->username;
        if (empty($username)) {
            $username = $request->cookie('arhana_comment_username');
        }
        if (empty($username)) {
            $username = 'Guest_' . rand(1000, 9999);
        }

        $username = strip_tags($username);
        $username = \Illuminate\Support\Str::limit($username, 30, '');

        $isAdminReply = auth()->check();

        \App\Models\SoftwareComment::create([
            'software_id' => $software->id,
            'parent_id' => $request->parent_id,
            'username' => $isAdminReply ? auth()->user()->name : $username,
            'type' => $request->input('type', 'comment'),
            'user_id' => $isAdminReply ? auth()->id() : null,
            'content' => strip_tags($request->content),
            'is_approved' => true,
        ]);

        $cookie = cookie('arhana_comment_username', $username, 60 * 24 * 365);

        return redirect()->back()
            ->with('success', 'Komentar / permintaan software Anda berhasil dikirim!')
            ->withCookie($cookie);
    }

    /**
     * Handle broken link report.
     */
    public function reportBrokenLink(Request $request, $softwareId)
    {
        $software = Software::findOrFail($softwareId);

        $request->validate([
            'reason' => 'required|string|max:150',
            'note' => 'nullable|string|max:500',
        ]);

        $reason = $request->reason;
        if ($request->filled('os')) {
            $meta = Software::getOsMeta($request->os);
            $reason = '[' . $meta['label'] . '] ' . $reason;
        }

        \App\Models\SoftwareReport::create([
            'software_id' => $software->id,
            'reason' => $reason,
            'note' => $request->note ? strip_tags($request->note) : null,
            'reporter_ip' => $request->ip(),
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Terima kasih atas laporannya! Tim kami akan segera memeriksa dan memperbaiki tautan unduhan software ini.');
    }

    /**
     * Handle download redirect & counter.
     */
    public function download(Request $request, $slug)
    {
        $software = Software::where('slug', $slug)->active()->firstOrFail();

        $software->increment('downloads_count');

        $os = $request->query('os');
        $downloadUrl = $os ? $software->getDownloadUrlForOs($os) : $software->download_url;

        return redirect()->away($downloadUrl);
    }

    /**
     * Instant search autocomplete suggestions.
     */
    public function suggest(Request $request)
    {
        $search = $request->query('search', '');
        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $softwares = Software::active()
            ->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('developer', 'like', '%' . $search . '%');
            })
            ->take(6)
            ->get(['slug', 'title', 'version', 'developer', 'icon']);

        $results = $softwares->map(function ($item) {
            return [
                'title' => $item->title,
                'slug' => $item->slug,
                'version' => $item->version,
                'developer' => $item->developer,
                'icon_url' => $item->icon_url,
                'url' => route('software.show', $item->slug),
            ];
        });

        return response()->json($results);
    }
}
