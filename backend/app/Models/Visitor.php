<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    protected $fillable = [
        'ip_address',
        'date',
        'city',
        'region',
        'country',
        'country_code',
        'last_page',
        'hits',
        'user_agent',
    ];

    protected $casts = [
        'date' => 'date',
        'hits' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Ensure date is always stored as Y-m-d string.
     */
    public function setDateAttribute($value): void
    {
        $this->attributes['date'] = $value ? \Carbon\Carbon::parse($value)->toDateString() : null;
    }

    /**
     * Get logs for this IP address.
     */
    public function logs()
    {
        return $this->hasMany(VisitorLog::class, 'ip_address', 'ip_address');
    }

    /**
     * Format location as readable string.
     */
    public function getLocationStringAttribute(): string
    {
        $parts = array_filter([$this->city, $this->region, $this->country]);
        return count($parts) > 0 ? implode(', ', $parts) : 'Tidak Diketahui';
    }

    /**
     * Get flag emoji.
     */
    public function getFlagAttribute(): string
    {
        return \App\Services\GeoIpService::countryFlag($this->country_code);
    }
}
