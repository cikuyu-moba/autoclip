<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PageView extends Model
{
    protected $fillable = [
        'ip_address',
        'viewable_id',
        'viewable_type',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Get the parent viewable model (Post, Product, or Project).
     */
    public function viewable(): MorphTo
    {
        return $this->morphTo();
    }
}
