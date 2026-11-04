<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Software extends Model
{
    use HasFactory;

    protected $table = 'softwares';

    protected $fillable = [
        'title',
        'slug',
        'category_id',
        'developer',
        'version',
        'license',
        'os_compatibility',
        'supported_os',
        'os_downloads',
        'file_size',
        'download_url',
        'official_website',
        'icon',
        'description',
        'features',
        'views_count',
        'downloads_count',
        'github_repo',
        'sync_source',
        'last_synced_at',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'views_count' => 'integer',
        'downloads_count' => 'integer',
        'supported_os' => 'array',
        'os_downloads' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function comments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SoftwareComment::class)->whereNull('parent_id')->with('replies')->latest();
    }

    public function reports(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SoftwareReport::class)->latest();
    }

    public function views(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(PageView::class, 'viewable');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeForOs($query, $os)
    {
        $os = strtolower($os);
        return $query->where(function ($q) use ($os) {
            $q->whereJsonContains('supported_os', $os)
              ->orWhere('os_compatibility', 'like', "%{$os}%");
        });
    }

    /**
     * Get array of supported OS slugs (e.g. ['windows', 'mac', 'linux', 'android']).
     */
    public function getSupportedOsListAttribute(): array
    {
        if (!empty($this->supported_os) && is_array($this->supported_os)) {
            return $this->supported_os;
        }

        // Fallback detection from os_compatibility string
        $list = ['windows'];
        $compat = strtolower($this->os_compatibility ?? '');
        if (str_contains($compat, 'mac') || str_contains($compat, 'apple') || str_contains($compat, 'os x')) {
            $list[] = 'mac';
        }
        if (str_contains($compat, 'linux') || str_contains($compat, 'ubuntu')) {
            $list[] = 'linux';
        }
        if (str_contains($compat, 'android')) {
            $list[] = 'android';
        }

        return array_unique($list);
    }

    /**
     * Get specific download URL for an OS, falling back to default download_url.
     */
    public function getDownloadUrlForOs(string $os): string
    {
        $os = strtolower($os);
        if (!empty($this->os_downloads) && is_array($this->os_downloads) && !empty($this->os_downloads[$os])) {
            return $this->os_downloads[$os];
        }

        return $this->download_url;
    }

    /**
     * Get OS metadata (icon, label, color).
     */
    public static function getOsMeta(string $os): array
    {
        return match (strtolower($os)) {
            'mac', 'macos' => ['slug' => 'mac', 'label' => 'macOS', 'icon' => '🍎', 'badge_bg' => 'rgba(148, 163, 184, 0.15)', 'badge_color' => '#64748b'],
            'linux' => ['slug' => 'linux', 'label' => 'Linux', 'icon' => '🐧', 'badge_bg' => 'rgba(234, 179, 8, 0.15)', 'badge_color' => '#ca8a04'],
            'android' => ['slug' => 'android', 'label' => 'Android', 'icon' => '🤖', 'badge_bg' => 'rgba(16, 185, 129, 0.15)', 'badge_color' => '#059669'],
            default => ['slug' => 'windows', 'label' => 'Windows', 'icon' => '🪟', 'badge_bg' => 'rgba(2, 132, 199, 0.15)', 'badge_color' => '#0284c7'],
        };
    }

    /**
     * Get displayable icon URL or fallback.
     */
    public function getIconUrlAttribute(): string
    {
        // 1. If admin explicitly uploaded custom icon to storage
        if (!empty($this->icon) && !filter_var($this->icon, FILTER_VALIDATE_URL) && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->icon)) {
            return asset('storage/' . $this->icon);
        }

        // 2. If admin explicitly entered an external URL
        if (!empty($this->icon) && filter_var($this->icon, FILTER_VALIDATE_URL)) {
            return $this->icon;
        }

        // 3. If explicitly pointing to public file path (e.g. images/software/...)
        if (!empty($this->icon) && file_exists(public_path($this->icon))) {
            return asset($this->icon);
        }

        // 4. Check if bundled local image exists matching slug
        foreach (['svg', 'png', 'jpg', 'webp', 'ico'] as $ext) {
            if (file_exists(public_path("images/software/{$this->slug}.{$ext}"))) {
                return asset("images/software/{$this->slug}.{$ext}");
            }
        }

        // 5. Fallback placeholder
        return asset('logo/logo.png');
    }
}
