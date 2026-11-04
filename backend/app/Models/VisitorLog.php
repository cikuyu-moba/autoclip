<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorLog extends Model
{
    protected $fillable = [
        'ip_address',
        'url',
        'page_name',
        'method',
        'city',
        'region',
        'country',
        'country_code',
        'user_agent',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Ensure date is always stored as Y-m-d string.
     */
    public function setDateAttribute($value): void
    {
        $this->attributes['date'] = $value ? \Carbon\Carbon::parse($value)->toDateString() : null;
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
