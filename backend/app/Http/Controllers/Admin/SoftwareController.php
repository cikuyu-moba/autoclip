<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Software;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class SoftwareController extends Controller
{
    /**
     * Display a listing of softwares.
     */
    public function index(Request $request)
    {
        $query = Software::with('category')->latest();

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('developer', 'like', "%{$search}%");
            });
        }

        $softwares = $query->paginate(15)->withQueryString();
        $categories = Category::where('type', 'software')->get();
        $pendingReportsCount = \App\Models\SoftwareReport::where('status', 'pending')->count();

        return view('admin.software.index', compact('softwares', 'categories', 'pendingReportsCount'));
    }

    /**
     * Show form for creating a new software.
     */
    public function create()
    {
        $categories = Category::where('type', 'software')->get();
        return view('admin.software.create', compact('categories'));
    }

    /**
     * Store newly created software.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'slug' => 'nullable|string|max:200|unique:softwares,slug',
            'category_id' => 'nullable|exists:categories,id',
            'developer' => 'nullable|string|max:150',
            'version' => 'required|string|max:50',
            'license' => 'required|string|max:50',
            'os_compatibility' => 'required|string|max:100',
            'supported_os' => 'nullable|array',
            'supported_os.*' => 'string|in:windows,mac,linux,android',
            'os_downloads' => 'nullable|array',
            'os_downloads.*' => 'nullable|string|max:1000',
            'file_size' => 'nullable|string|max:50',
            'download_url' => 'required|string|max:1000',
            'official_website' => 'nullable|string|max:500',
            'icon_file' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'icon_url' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'features' => 'nullable|string',
            'github_repo' => 'nullable|string|max:150',
            'sync_source' => 'required|string|in:manual,github',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['slug'] = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_active'] = $request->has('is_active');

        // Handle OS Support
        $supportedOs = $request->input('supported_os', ['windows']);
        $validated['supported_os'] = !empty($supportedOs) ? array_values((array)$supportedOs) : ['windows'];

        $osDownloads = $request->input('os_downloads', []);
        if (is_array($osDownloads)) {
            $cleaned = array_filter($osDownloads, fn($url) => !empty(trim($url ?? '')));
            $validated['os_downloads'] = !empty($cleaned) ? $cleaned : null;
        }

        // Handle icon
        if ($request->hasFile('icon_file')) {
            $validated['icon'] = $request->file('icon_file')->store('softwares', 'public');
        } elseif (!empty($request->input('icon_url'))) {
            $validated['icon'] = $request->input('icon_url');
        }

        unset($validated['icon_file'], $validated['icon_url']);

        $software = Software::create($validated);

        // If github repo provided and sync_source is github, trigger initial sync
        if ($software->sync_source === 'github' && !empty($software->github_repo)) {
            try {
                Artisan::call('software:sync', ['--slug' => $software->slug]);
            } catch (\Exception $e) {
                // Ignore sync error during creation
            }
        }

        return redirect()->route('admin.software.index')->with('success', "Software '{$software->title}' berhasil ditambahkan.");
    }

    /**
     * Show form for editing software.
     */
    public function edit(Software $software)
    {
        $categories = Category::where('type', 'software')->get();
        return view('admin.software.edit', compact('software', 'categories'));
    }

    /**
     * Update existing software.
     */
    public function update(Request $request, Software $software)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'slug' => "nullable|string|max:200|unique:softwares,slug,{$software->id}",
            'category_id' => 'nullable|exists:categories,id',
            'developer' => 'nullable|string|max:150',
            'version' => 'required|string|max:50',
            'license' => 'required|string|max:50',
            'os_compatibility' => 'required|string|max:100',
            'supported_os' => 'nullable|array',
            'supported_os.*' => 'string|in:windows,mac,linux,android',
            'os_downloads' => 'nullable|array',
            'os_downloads.*' => 'nullable|string|max:1000',
            'file_size' => 'nullable|string|max:50',
            'download_url' => 'required|string|max:1000',
            'official_website' => 'nullable|string|max:500',
            'icon_file' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'icon_url' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'features' => 'nullable|string',
            'github_repo' => 'nullable|string|max:150',
            'sync_source' => 'required|string|in:manual,github',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['slug'] = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']);
        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_active'] = $request->has('is_active');

        // Handle OS Support
        $supportedOs = $request->input('supported_os', ['windows']);
        $validated['supported_os'] = !empty($supportedOs) ? array_values((array)$supportedOs) : ['windows'];

        $osDownloads = $request->input('os_downloads', []);
        if (is_array($osDownloads)) {
            $cleaned = array_filter($osDownloads, fn($url) => !empty(trim($url ?? '')));
            $validated['os_downloads'] = !empty($cleaned) ? $cleaned : null;
        }

        // Handle icon
        if ($request->hasFile('icon_file')) {
            // Delete old file if stored locally
            if (!empty($software->icon) && !filter_var($software->icon, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($software->icon)) {
                Storage::disk('public')->delete($software->icon);
            }
            $validated['icon'] = $request->file('icon_file')->store('softwares', 'public');
        } elseif (!empty($request->input('icon_url'))) {
            $validated['icon'] = $request->input('icon_url');
        }

        unset($validated['icon_file'], $validated['icon_url']);

        $software->update($validated);

        return redirect()->route('admin.software.index')->with('success', "Software '{$software->title}' berhasil diperbarui.");
    }

    /**
     * Delete software.
     */
    public function destroy(Software $software)
    {
        $title = $software->title;
        if (!empty($software->icon) && !filter_var($software->icon, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($software->icon)) {
            Storage::disk('public')->delete($software->icon);
        }

        $software->delete();

        return redirect()->route('admin.software.index')->with('success', "Software '{$title}' berhasil dihapus.");
    }

    /**
     * Discover and auto-add new softwares from candidates or specific GitHub repo.
     */
    public function discover(Request $request)
    {
        try {
            $repo = $request->input('repo');
            $limit = (int) $request->input('limit', 3);

            $countBefore = Software::count();

            $params = ['--limit' => $limit];
            if (!empty($repo)) {
                $params['--repo'] = trim($repo);
            }

            Artisan::call('software:discover', $params);
            $output = Artisan::output();

            $countAfter = Software::count();
            $added = $countAfter - $countBefore;

            if ($added > 0) {
                return back()->with('success', "Berhasil memindai dan menambahkan {$added} software baru ke katalog!");
            }

            if (str_contains($output, 'Rate Limit')) {
                return back()->with('warning', 'Batas kuota (Rate Limit) GitHub API tercapai. Silakan coba beberapa saat lagi.');
            }

            if (!empty($repo)) {
                return back()->with('info', "Software dari repositori '{$repo}' sudah ada di katalog atau tidak ditemukan rilis yang valid.");
            }

            return back()->with('info', 'Pemindaian selesai. Seluruh software kandidat yang tersedia saat ini sudah ada di katalog Anda.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memindai software: ' . $e->getMessage());
        }
    }

    /**
     * Display broken link reports.
     */
    public function reportsIndex(Request $request)
    {
        $query = \App\Models\SoftwareReport::with('software')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reports = $query->paginate(15)->withQueryString();
        $pendingCount = \App\Models\SoftwareReport::where('status', 'pending')->count();

        return view('admin.software.reports', compact('reports', 'pendingCount'));
    }

    /**
     * Mark a broken link report as resolved.
     */
    public function resolveReport(\App\Models\SoftwareReport $report)
    {
        $report->update(['status' => 'resolved']);
        return back()->with('success', "Laporan untuk software '{$report->software->title}' ditandai selesai.");
    }

    /**
     * Delete report.
     */
    public function destroyReport(\App\Models\SoftwareReport $report)
    {
        $report->delete();
        return back()->with('success', "Laporan berhasil dihapus.");
    }

    /**
     * Sync single software with GitHub API now.
     */
    public function syncSingle(Software $software)
    {
        if (empty($software->github_repo)) {
            return back()->with('error', "Software ini tidak memiliki repositori GitHub terdaftar.");
        }

        try {
            Artisan::call('software:sync', ['--slug' => $software->slug]);
            $output = trim(Artisan::output());

            if (str_contains($output, 'Rate Limit')) {
                return back()->with('error', "Gagal sinkronisasi: GitHub API Rate Limit tercapai. Silakan coba lagi beberapa saat atau tambahkan GITHUB_TOKEN di file .env.");
            }

            $software->refresh();
            return back()->with('success', "Software '{$software->title}' berhasil disinkronkan ke versi terbaru: {$software->version}.");
        } catch (\Exception $e) {
            return back()->with('error', "Gagal sinkronisasi: " . $e->getMessage());
        }
    }

    /**
     * Sync all softwares.
     */
    public function syncAll()
    {
        try {
            Artisan::call('software:sync');
            $output = trim(Artisan::output());

            if (str_contains($output, 'Rate Limit')) {
                return back()->with('warning', "Sinkronisasi sebagian software dibatalkan karena GitHub API Rate Limit tercapai. Silakan coba lagi beberapa saat atau tambahkan GITHUB_TOKEN di file .env.");
            }

            return back()->with('success', "Seluruh software GitHub berhasil disinkronkan ke rilis terbaru!");
        } catch (\Exception $e) {
            return back()->with('error', "Gagal sinkronisasi massal: " . $e->getMessage());
        }
    }
}
