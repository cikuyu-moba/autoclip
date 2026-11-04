<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SoftwareReport extends Model
{
    protected $fillable = [
        'software_id',
        'reason',
        'note',
        'reporter_ip',
        'status',
    ];

    public function software(): BelongsTo
    {
        return $this->belongsTo(Software::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }
}
